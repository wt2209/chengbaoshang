<?php

namespace App\Exports\Sheets;

use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportDetailSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnFormatting
{
    private $month;
    private $year;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function headings(): array
    {
        return [
            ["{$this->year}年{$this->month}月份承包商公寓水电及服务费明细表"],
            ['出表单位：服务中心', '', '', '', '', '', '', '', '', '', '', '', '',  '', '', '日期：' . date('Y-m-d')],
            ['房间号', '公司名', '住宿起止时间', '水电费用', '', '', '', '', '', '', '', '', '服务费', '减免额度', '实收服务费', '总金额'],
            ['', '', '', '电费', '', '', '', '水费', '', '', '', '水电费合计', '', '', '', ''],
            ['', '', '', '上期数', '本期数', '用电量', '电费', '上期数', '本期数', '用水量', '水费', '', '', '', '', ''],
        ];
    }

    public function collection()
    {
        $reports = Report::with(['record.company', 'record.room'])
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get()
            ->toArray();
        $data = [];
        foreach ($reports as $report) {
            if (isset($data[$report['record']['company']['company_name']])) {
                $data[$report['record']['company']['company_name']][] = $report;
            } else {
                $data[$report['record']['company']['company_name']] = [$report];
            }
        }
        $rows = [];
        foreach ($data as $companyName => $companyReports) {
            $electricAmount = 0;
            $electricMoney = 0;
            $waterAmount = 0;
            $waterMoney = 0;
            $rentMoney = 0;
            $actualRentMoney = 0;
            foreach ($companyReports as $r) {
                $electricAmount += $r['electric_amount'];
                $electricMoney += $r['electric_money'];
                $waterAmount += $r['water_amount'];
                $waterMoney += $r['water_money'];
                $rentMoney += $r['rent'];
                $actualRentMoney += $r['actual_rent'];
                $rows[] = [
                    $r['record']['room']['title'],
                    $r['record']['company']['company_name'],
                    $r['start_date'] . '—' . $r['end_date'],
                    $r['pre_electric_base'],
                    $r['current_electric_base'],
                    $r['electric_amount'],
                    $r['electric_money'],
                    $r['pre_water_base'],
                    $r['current_water_base'],
                    $r['water_amount'],
                    $r['water_money'],
                    $r['water_money'] + $r['electric_money'],
                    $r['rent'],
                    $r['rent_discount'],
                    $r['actual_rent'],
                    $r['actual_rent'] + $r['water_money'] + $r['electric_money'],
                ];
            }
            $rows[] = [
                '',
                $companyName . ' 汇总',
                '',
                '',
                '',
                $electricAmount,
                $electricMoney,
                '',
                '',
                $waterAmount,
                $waterMoney,
                $waterMoney + $electricMoney,
                $rentMoney,
                '',
                $actualRentMoney,
                $waterMoney + $electricMoney + $actualRentMoney,
            ];
        }
        return new Collection($rows);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('O2:P2');
        $sheet->mergeCells('A3:A5');
        $sheet->mergeCells('B3:B5');
        $sheet->mergeCells('C3:C5');
        $sheet->mergeCells('D3:L3');
        $sheet->mergeCells('D4:G4');
        $sheet->mergeCells('H3:K3');
        $sheet->mergeCells('L4:L5');
        $sheet->mergeCells('M3:M5');
        $sheet->mergeCells('N3:N5');
        $sheet->mergeCells('O3:O5');
        $sheet->mergeCells('P3:P5');
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    public function title(): string
    {
        return '明细表';
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }
}
