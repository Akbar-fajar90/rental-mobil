<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LaporanModel;

class Laporan extends BaseController
{
    protected LaporanModel $laporanModel;
    
    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        helper(['form', 'url', 'asset']);
    }
    
    // ========== HALAMAN UTAMA ==========
    public function index()
    {
        // Get filters from request
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'status' => $this->request->getGet('status'),
            'search' => $this->request->getGet('search')
        ];
        
        // Pagination
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $totalData = $this->laporanModel->getLaporanCount(array_filter($filters));
        $totalPages = ceil($totalData / $perPage);
        
        $data = [
            'title' => 'Laporan Operasional',
            'page_title' => 'Laporan',
            'active_menu' => 'laporan',
            'summary' => $this->laporanModel->getSummary(),
            'laporan' => $this->laporanModel->getLaporan($perPage, $offset, array_filter($filters)),
            'chart_data' => $this->laporanModel->getChartData(),
            'filters' => $filters,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_data' => $totalData,
            'per_page' => $perPage
        ];
        
        return view('admin/laporan', $data);
    }
    
    // ========== EXPORT KE EXCEL ==========
    public function exportExcel()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'status' => $this->request->getGet('status'),
            'search' => $this->request->getGet('search')
        ];
        
        $laporan = $this->laporanModel->getLaporan(10000, 0, array_filter($filters));
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $headers = ['Tanggal', 'ID Sewa', 'Pelanggan', 'Mobil', 'Plat Nomor', 'Pendapatan', 'Denda', 'Status'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i) . '1', $header);
        }
        
        // Style header
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2563eb');
        $sheet->getStyle('A1:H1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Data
        $row = 2;
        foreach ($laporan as $data) {
            $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($data->tgl_sewa)));
            $sheet->setCellValue('B' . $row, $data->id_sewa);
            $sheet->setCellValue('C' . $row, $data->nama_pelanggan);
            $sheet->setCellValue('D' . $row, $data->mobil_merk);
            $sheet->setCellValue('E' . $row, $data->plat_nomor ?? '-');
            $sheet->setCellValue('F' . $row, $data->pendapatan ?? 0);
            $sheet->setCellValue('G' . $row, ($data->denda_terlambat ?? 0) + ($data->denda_kerusakan ?? 0));
            $sheet->setCellValue('H' . $row, $data->status);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Clean output buffer
        if (ob_get_length()) ob_end_clean();
        
        $filename = 'laporan_operasional_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    
    // ========== EXPORT KE PDF ==========
    public function exportPdf()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'status' => $this->request->getGet('status'),
            'search' => $this->request->getGet('search')
        ];
        
        $laporan = $this->laporanModel->getLaporan(10000, 0, array_filter($filters));
        $summary = $this->laporanModel->getSummary();
        
        $html = view('admin/laporan_pdf', ['laporan' => $laporan, 'summary' => $summary]);
        
        // Clean output buffer
        if (ob_get_length()) ob_end_clean();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'laporan_operasional_' . date('Y-m-d_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }
}