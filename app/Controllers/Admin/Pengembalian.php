<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengembalianModel;
use App\Models\PenyewaanModel;

class Pengembalian extends BaseController
{
    protected $pengembalianModel;
    protected $penyewaanModel;

    // ========== CONSTRUCTOR (harus di atas sebelum semua method) ==========
    public function __construct()
    {
        $this->pengembalianModel = new PengembalianModel();
        $this->penyewaanModel = new PenyewaanModel();
        helper(['asset', 'form', 'url']);
    }

    // ========== HALAMAN UTAMA ==========
    public function index()
    {
        // Get all active rentals (belum dikembalikan)
        $activeRentals = $this->penyewaanModel
            ->select('t_sewa.*, t_pelanggan.nama as nama_pelanggan, t_pelanggan.no_telp, t_pelanggan.email, t_mobil.merk as mobil_merk, t_mobil.plat_nomor, t_mobil.tarif_per_hari, t_mobil.denda_per_hari, t_mobil.foto_mobil')
            ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
            ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
            ->where('t_sewa.status', 'Berlangsung')
            ->where('t_sewa.status_pengajuan', 'disetujui')
            ->findAll();

        // Get return history
        $history = $this->pengembalianModel->getHistory(10);

        // Get stats
        $stats = [
            'total_returned' => $this->pengembalianModel->countAll(),
            'total_fines'    => $this->pengembalianModel->selectSum('denda_terlambat')->get()->getRow()->denda_terlambat ?? 0,
            'total_damage'   => $this->pengembalianModel->selectSum('denda_kerusakan')->get()->getRow()->denda_kerusakan ?? 0
        ];

        $data = [
            'title'          => 'Pengembalian Mobil',
            'page_title'     => 'Pengembalian',
            'active_menu'    => 'pengembalian',
            'active_rentals' => $activeRentals,
            'history'        => $history,
            'stats'          => (object)$stats
        ];

        return view('admin/pengembalian', $data);
    }

    // ========== DETAIL PENGEMBALIAN UNTUK MODAL ==========
    public function detail($id_sewa)
    {
        $rental = $this->penyewaanModel
            ->select('t_sewa.*, t_pelanggan.nama as nama_pelanggan, t_pelanggan.no_telp, t_pelanggan.email, t_pelanggan.tgl_daftar, t_mobil.merk as mobil_merk, t_mobil.plat_nomor, t_mobil.tarif_per_hari, t_mobil.denda_per_hari, t_mobil.foto_mobil')
            ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
            ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
            ->where('t_sewa.id_sewa', $id_sewa)
            ->first();

        if (!$rental) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
        }

        // Calculate late fee
        $today   = new \DateTime();
        $rencana = new \DateTime($rental->tgl_kembali_rencana);
        $lateDays = 0;
        $lateFee  = 0;

        if ($today > $rencana) {
            $diff     = $rencana->diff($today);
            $lateDays = $diff->days;
            $lateFee  = $lateDays * ($rental->denda_per_hari ?? 100000);
        }

        return $this->response->setJSON([
            'rental'    => $rental,
            'late_days' => $lateDays,
            'late_fee'  => $lateFee
        ]);
    }

    // ========== PROSES PENGEMBALIAN ==========
    public function process()
    {
        // Validasi input
        $rules = [
            'id_sewa'         => 'required|integer',
            'kondisi_mobil'   => 'required|in_list[baik,rusak-ringan,rusak-berat]',
            'denda_kerusakan' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id_sewa       = (int) $this->request->getPost('id_sewa');
        $kondisi_mobil = $this->request->getPost('kondisi_mobil');
        $input_denda   = $this->request->getPost('denda_kerusakan');
        $manual_cost   = (float) str_replace(['Rp', '.', ','], '', $input_denda ? (string)$input_denda : '0');

        /** @var object|null $rental */
        $rental = $this->penyewaanModel
            ->select('t_sewa.*, t_mobil.tarif_per_hari, t_mobil.denda_per_hari')
            ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
            ->find($id_sewa);

        if (!$rental) {
            return redirect()->back()->with('error', 'Data sewa tidak ditemukan');
        }

        // Calculate fees
        $today   = date('Y-m-d H:i:s');
        $lateFee = $this->pengembalianModel->calculateLateFee(
            $rental->tgl_kembali_rencana,
            $today,
            $rental->denda_per_hari ?? 100000
        );

        $damageFee = $this->pengembalianModel->calculateDamageFee(
            $rental->tarif_per_hari,
            $kondisi_mobil,
            $manual_cost
        );

        // Process return
        $data = [
            'id_sewa'         => $id_sewa,
            'id_pegawai'      => session()->get('adminId') ?? 1,
            'tgl_kembali'     => $today,
            'kondisi_mobil'   => $kondisi_mobil,
            'denda_terlambat' => $lateFee,
            'denda_kerusakan' => $damageFee
        ];

        if ($this->pengembalianModel->processReturn($id_sewa, $data)) {
            return redirect()->to('/admin/pengembalian')->with('success', 'Pengembalian berhasil diproses! Total Denda: Rp ' . number_format($lateFee + $damageFee, 0, ',', '.'));
        } else {
            return redirect()->back()->with('error', 'Gagal memproses pengembalian');
        }
    }

    // ========== EXPORT KE EXCEL ==========
    public function exportExcel()
    {
        $history = $this->pengembalianModel->getHistory(1000);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'ID Pengembalian');
        $sheet->setCellValue('B1', 'ID Sewa');
        $sheet->setCellValue('C1', 'Pelanggan');
        $sheet->setCellValue('D1', 'Mobil');
        $sheet->setCellValue('E1', 'Plat Nomor');
        $sheet->setCellValue('F1', 'Tanggal Kembali');
        $sheet->setCellValue('G1', 'Kondisi');
        $sheet->setCellValue('H1', 'Denda Terlambat');
        $sheet->setCellValue('I1', 'Denda Kerusakan');
        $sheet->setCellValue('J1', 'Total Denda');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1d63ed']
            ]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($history as $item) {
            $sheet->setCellValue('A' . $row, $item->id_pengembalian);
            $sheet->setCellValue('B' . $row, $item->id_sewa);
            $sheet->setCellValue('C' . $row, $item->nama_pelanggan);
            $sheet->setCellValue('D' . $row, $item->mobil_merk);
            $sheet->setCellValue('E' . $row, $item->plat_nomor ?? '-');
            $sheet->setCellValue('F' . $row, date('d/m/Y H:i', strtotime($item->tgl_kembali)));

            $kondisiText = [
                'baik'        => 'Baik',
                'rusak-ringan' => 'Rusak Ringan',
                'rusak-berat' => 'Rusak Berat'
            ][$item->kondisi_mobil] ?? $item->kondisi_mobil;
            $sheet->setCellValue('G' . $row, $kondisiText);

            $sheet->setCellValue('H' . $row, $item->denda_terlambat ?? 0);
            $sheet->setCellValue('I' . $row, $item->denda_kerusakan ?? 0);
            $sheet->setCellValue('J' . $row, ($item->denda_terlambat ?? 0) + ($item->denda_kerusakan ?? 0));

            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'return_history_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    // ========== EXPORT KE WORD ==========
    public function exportWord()
    {
        $history = $this->pengembalianModel->getHistory(1000);

        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Laporan Pengembalian Mobil</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #1d63ed; text-align: center; }
            .header-info { margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #1d63ed; color: white; padding: 10px; border: 1px solid #ddd; }
            td { padding: 8px; border: 1px solid #ddd; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <h1>LAPORAN PENGEMBALIAN MOBIL</h1>
        <div class="header-info">
            <p><strong>Tanggal Export:</strong> ' . date('d/m/Y H:i:s') . '</p>
            <p><strong>Total Data:</strong> ' . count($history) . ' pengembalian</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>ID Sewa</th><th>Pelanggan</th><th>Mobil</th>
                    <th>Plat Nomor</th><th>Tgl Kembali</th><th>Kondisi</th>
                    <th>Denda Terlambat</th><th>Denda Kerusakan</th><th>Total Denda</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($history as $item) {
            $kondisiText = [
                'baik'        => 'Baik',
                'rusak-ringan' => 'Rusak Ringan',
                'rusak-berat' => 'Rusak Berat'
            ][$item->kondisi_mobil] ?? $item->kondisi_mobil;

            $html .= '
            <tr>
                <td>' . $item->id_pengembalian . '</td>
                <td>' . $item->id_sewa . '</td>
                <td>' . htmlspecialchars($item->nama_pelanggan) . '</td>
                <td>' . htmlspecialchars($item->mobil_merk) . '</td>
                <td>' . ($item->plat_nomor ?? '-') . '</td>
                <td>' . date('d/m/Y H:i', strtotime($item->tgl_kembali)) . '</td>
                <td>' . $kondisiText . '</td>
                <td style="text-align: right;">Rp ' . number_format($item->denda_terlambat ?? 0, 0, ',', '.') . '</td>
                <td style="text-align: right;">Rp ' . number_format($item->denda_kerusakan ?? 0, 0, ',', '.') . '</td>
                <td style="text-align: right;">Rp ' . number_format(($item->denda_terlambat ?? 0) + ($item->denda_kerusakan ?? 0), 0, ',', '.') . '</td>
            </tr>';
        }

        $html .= '
            </tbody>
        </table>
        <div class="footer"><p>Generated by Sistem Rental Mobil</p></div>
    </body>
    </html>';

        $filename = 'return_history_' . date('Y-m-d_His') . '.doc';
        header('Content-Type: application/msword');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $html;
        exit();
    }

    // ========== EXPORT KE PDF ==========
    public function exportPdf()
    {
        $history = $this->pengembalianModel->getHistory(1000);

        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Laporan Pengembalian Mobil</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #1d63ed; text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #1d63ed; color: white; padding: 10px; border: 1px solid #ddd; }
            td { padding: 8px; border: 1px solid #ddd; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <h1>LAPORAN PENGEMBALIAN MOBIL</h1>
        <p><strong>Tanggal Export:</strong> ' . date('d/m/Y H:i:s') . '</p>
        <p><strong>Total Data:</strong> ' . count($history) . ' pengembalian</p>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>ID Sewa</th><th>Pelanggan</th><th>Mobil</th>
                    <th>Tgl Kembali</th><th>Kondisi</th><th>Total Denda</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($history as $item) {
            $kondisiText = [
                'baik'        => 'Baik',
                'rusak-ringan' => 'Rusak Ringan',
                'rusak-berat' => 'Rusak Berat'
            ][$item->kondisi_mobil] ?? $item->kondisi_mobil;

            $html .= '
            <tr>
                <td>' . $item->id_pengembalian . '</td>
                <td>' . $item->id_sewa . '</td>
                <td>' . htmlspecialchars($item->nama_pelanggan) . '</td>
                <td>' . htmlspecialchars($item->mobil_merk) . '</td>
                <td>' . date('d/m/Y', strtotime($item->tgl_kembali)) . '</td>
                <td>' . $kondisiText . '</td>
                <td style="text-align: right;">Rp ' . number_format(($item->denda_terlambat ?? 0) + ($item->denda_kerusakan ?? 0), 0, ',', '.') . '</td>
            </tr>';
        }

        $html .= '
            </tbody>
        </table>
        <div class="footer"><p>Generated by Sistem Rental Mobil</p></div>
    </body>
    </html>';

        require_once FCPATH . 'vendor/autoload.php';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'return_history_' . date('Y-m-d_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }
}
