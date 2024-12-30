<?php

namespace Think;

class Csv
{
    public function exportCsv($data, $tableHeader, $filename)
    {
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: max-age=0');
        // header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        // header('Content-Disposition: attachment;filename="' . $filename . '"');



        $file = fopen('php://output', "a");

        $tableHeader = array_filter($tableHeader, function ($header) {
            return $header !== '照片';
        });

        $tableHeader[] = '定金截图';
        $tableHeader[] = '全款截图';

        $headerList = array_map(function ($header) {
            return iconv('UTF-8', 'GB2312//IGNORE', $header);
        }, $tableHeader);

        fputcsv($file, $headerList);

        $limit = 1000;
        $calc = 0;

        $memberPayments = []; // 用于存储每个发布人的全款总和


        //高质量个人写作
        $personWritePayments = [];
        //渠道写作
        $groupWritePayments = [];
        //非公司期刊发表
        $Nocorporatepublication = [];
        //公司期刊发表
        $corporatepublication = [];
        //会议
        $caPayments = [];
        //scopus检索
        $scopusPayments = [];
        //scopus不检索
        $noscopusPayments = [];
        //eassy
        $eassyPatments = [];
        //运营/淘宝推荐单
        $taobaoPayments = [];
        $summaryData = []; // 存储汇总数据
        $receiverData = []; // 对接数据

        foreach ($data as $key => $row) {
            $calc++;
            if ($calc == $limit) {
                ob_flush();
                flush();
                $calc = 0;
            }

            $memberId = $row['member_id'];
            $recipient_id = $row['recipient_id'];
            $fullPayment = $row['4']; // 全款
            // 投稿期刊类型
            $journalType = trim($row['0']);
            // 提交稿件的类型
            $submitType = trim($row['1']);
            // 开单地
            $whereSubmit = trim($row['2']);
            // 多收金额
            $payMore = trim($row['6']);

            // echo "<pre>Debug Info:";
            // echo "\nActual SubmitType: "; var_dump($whereSubmit);
            // echo "\nExpected SubmitType: "; var_dump('南京');
            // echo "</pre>";


            if (!isset($summaryData[$memberId])) {
                $summaryData[$memberId] = [
                    '高质量' => 0, '高质量提成' => 0,
                    '渠道写作' => 0, '渠道写作提成' => 0,
                    '公司期刊发表' => 0, '公司期刊发表提成' => 0,
                    '会议' => 0, '会议提成' => 0,
                    'scopus检索' => 0, 'scopus检索提成' => 0,
                    'scopus不检索' => 0, 'scopus不检索提成' => 0,
                    'eassy' => 0, 'eassy提成' => 0,
                    '运营/淘宝推荐单' => 0, '运营/淘宝推荐单提成' => 0,
                    '总提成' => 0
                ];
            }

            if (isset($row['photos'])) {
                $photos = $row['photos'];
                unset($row['photos']);

                $parts = explode(' | ', $photos);
                $depositMatch = trim(substr($parts[0], strpos($parts[0], 'http://')));
                $fullPaymentMatch = trim(substr($parts[1], strpos($parts[1], 'http://')));

                $row['定金截图'] = $depositMatch;
                $row['全款截图'] = $fullPaymentMatch;
            }
            if ($whereSubmit == '南京') {
                $this->updatePaymentsSummary($summaryData[$memberId], $submitType, $journalType, $fullPayment, $memberId, $receiverData, $recipient_id);
            } else {
                $this->xuzhouupdatePaymentsSummary($summaryData[$memberId], $submitType, $journalType, $fullPayment, $memberId,  $payMore,$receiverData, $recipient_id);
            }


            $row = array_values($row);
            $encodedRow = array_map(function ($item) {
                return iconv('UTF-8', 'GB2312//IGNORE', $item);
            }, $row);

            fputcsv($file, $encodedRow);
        }

        // 写入汇总数据
        $this->writeSummaryData($file, $summaryData);
        $this->writeReceiverData($file, $receiverData);

        fclose($file);
        exit;
    }
    //南京scopus检索
    private function calculateCommission($amount)
    {
        if ($amount <= 50000) {
            return 0.02; // 2%
        } elseif ($amount <= 100000) {
            return 0.05; // 5%
        } elseif ($amount <= 150000) {
            return 0.10; // 10%
        } elseif ($amount <= 200000) {
            return 0.15; // 15%
        } elseif ($amount <= 250000) {
            return 0.18; // 18%
        } else {
            return 0.20; // 20%
        }
    }
    private function xuzhoucalculateCommission($amount)
    {
        if ($amount <= 50000) {
            return 0.03; // 2%
        } elseif ($amount <= 100000) {
            return 0.05; // 5%
        } else {
            return 0.10; // 10%
        }
    }
    // easy定制
    private function easyWrite($memberId, $eassyPatments)
    {
        if ($memberId == '张丹丹' || $memberId == '张冬冬' || $memberId == '钱燕燕' || $memberId == '毕芸霏') {
            return 16;
        } elseif ($memberId == '花永超') {
            return 20;
        } else {
            return 10;
        }
    }
    private function xuzhoueasyWrite($memberId, $eassyPatments)
    {
        if ($memberId == '吕士钦') {
            return 15;
        } elseif ($memberId == '胡滨涛' && $eassyPatments > 50000) {
            return 10;
        } else {
            if ($eassyPatments <= 20000) {
                return 5;
            } elseif ($eassyPatments > 20000 && $eassyPatments <= 50000) {
                return 7;
            } else {
                return 10;
            }
        }
    }
    function writeSummaryData($file, $summaryData)
    {
        fputcsv($file, []); // 写入空行作为分隔
        $summaryHeader = ["发布人", "高质量", "提成", "渠道写作", "提成", "公司期刊发表", "提成", "会议", "提成", "scopus检索", "提成", "scopus不检索", "提成", "eassy", "提成", "运营/淘宝推荐单", "提成", "总提成"];
        $encodedSummaryHeader = array_map(function ($header) {
            return iconv('UTF-8', 'GB2312//IGNORE', $header);
        }, $summaryHeader);
        fputcsv($file, $encodedSummaryHeader);

        foreach ($summaryData as $memberId => $data) {
            $row = [$memberId];
            foreach ($data as $value) {
                $row[] = $value;
            }
            $encodedRow = array_map(function ($item) {
                return iconv('UTF-8', 'GB2312//IGNORE', $item);
            }, $row);
            fputcsv($file, $encodedRow);
        }
    }

    function writeReceiverData($file, $receiverData)
    {
        fputcsv($file, []); // 写入空行作为分隔
        $summaryHeader = ["对接人", "提成"];
        $encodedSummaryHeader = array_map(function ($header) {
            return iconv('UTF-8', 'GB2312//IGNORE', $header);
        }, $summaryHeader);
        fputcsv($file, $encodedSummaryHeader);

        foreach ($receiverData as $recipient_id => $commission) {
            $row = [$recipient_id]; // 初始化row数组
            if ($recipient_id == 'Essay对接') {
                $row[] = $commission; // 使用已经计算好的提成
            } else {
                $row[] = $commission * 50; // 使用基础提成乘以50
            }
            $encodedRow = array_map(function ($item) {
                return iconv('UTF-8', 'GB2312//IGNORE', $item);
            }, $row);
            fputcsv($file, $encodedRow);
        }
    }

    function updatePaymentsSummary(&$summary, $submitType, $journalType, $fullPayment, $memberId, &$receiverData, $recipient_id)
    {
        if ($journalType == 'ES') {
            $commission = $fullPayment * 0.01; // Eassy的提成计算
        } else {
            $commission = 1; // 其他情况使用基数1（外部乘以50）
        }

        if (!isset($receiverData[$recipient_id])) {
            $receiverData[$recipient_id] = $commission;
        } else {
            $receiverData[$recipient_id] += $commission;
        }
        // 高质量个人写作
        if ($submitType == '高质量写作') {
            $summary['高质量'] += $fullPayment;
            $summary['高质量提成'] += $fullPayment * 0.1;
            $summary['总提成'] += $fullPayment * 0.1;
        }

        // 渠道写作
        elseif ($submitType == '渠道写作') {
            $summary['渠道写作'] += $fullPayment;
            $summary['渠道写作提成'] += $fullPayment * 0.02;
            $summary['总提成'] += $fullPayment * 0.02;
        }

        // 发表（写发），包括SSCI, SCI, AHCI, 非EI
        elseif ($submitType == '发表/写发' && ($journalType == 'SSCI' || $journalType == 'SCI' || $journalType == 'AHCI' || $journalType == 'EI')) {
            $summary['公司期刊发表'] += $fullPayment;
            $summary['公司期刊发表提成'] += 1000;
            $summary['总提成'] += 1000;
        }

        // 运营/淘宝推荐单
        elseif ($submitType == '运营/淘宝推荐单') {
            $summary['运营/淘宝推荐单'] += $fullPayment;
            $summary['运营/淘宝推荐单提成'] += $fullPayment * 0.05;
            $summary['总提成'] += $fullPayment * 0.05;
        }

        // Scopus不检索
        elseif ($journalType == 'Scopus不检索') {
            $summary['scopus不检索'] += $fullPayment;
            $summary['scopus不检索提成'] += $fullPayment * 0.1;
            $summary['总提成'] += $fullPayment * 0.1;
        }

        // CA会议
        elseif ($journalType == 'CA') {
            $summary['会议'] += $fullPayment;
            $summary['会议提成'] += $fullPayment * 0.05;
            $summary['总提成'] += $fullPayment * 0.05;
        }

        // eassy
        elseif ($journalType == 'ES') {
            $summary['eassy'] += $fullPayment;
            $newCommissionRate = $this->easyWrite($memberId, $summary['eassy']);
            $eassyCommission = $summary['eassy']  * ($newCommissionRate / 100);
            $summary['eassy提成'] = $eassyCommission;
            $summary['总提成'] += $eassyCommission;
        }

        // SCOPUS检索
        elseif ($journalType == 'SCOPUS') {
            $summary['scopus检索'] += $fullPayment;
            $newCommissionRate = $this->calculateCommission($summary['scopus检索']);
            $scopusCommission = $summary['scopus检索'] * $newCommissionRate;
            $summary['scopus检索提成'] = $scopusCommission;
            $summary['总提成'] += $scopusCommission;
        }

        // 总提成更新在此时完成，确保包含了所有单项提成
    }
    function xuzhouupdatePaymentsSummary(&$summary, $submitType, $journalType, $fullPayment, $memberId, $payMore, &$receiverData, $recipient_id)
    {
        // 从 fullPayment 中减去 payMore 部分
        $adjustedFullPayment = $fullPayment - $payMore;
    
        if ($journalType == 'ES') {
            $commission = $adjustedFullPayment * 0.01; // Eassy的提成计算
        } else {
            $commission = 1; // 其他情况使用基数1（外部乘以50）
        }
    
        if (!isset($receiverData[$recipient_id])) {
            $receiverData[$recipient_id] = $commission;
        } else {
            $receiverData[$recipient_id] += $commission;
        }
    
        // 渠道写作
        if ($submitType == '渠道写作') {
            $summary['渠道写作'] += $adjustedFullPayment;
            $summary['渠道写作提成'] += $adjustedFullPayment * 0.02;
            $summary['总提成'] += $adjustedFullPayment * 0.02;
        }
        // 发表（写发），包括SSCI, SCI, AHCI, 非EI
        elseif ($submitType == '发表/写发' && ($journalType == 'SSCI' || $journalType == 'SCI' || $journalType == 'AHCI' || $journalType == 'EI')) {
            $summary['公司期刊发表'] += $adjustedFullPayment;
            $summary['公司期刊发表提成'] += 1000;
            $summary['总提成'] += 1000;
        }
        // eassy
        elseif ($journalType == 'ES') {
            $summary['eassy'] += $adjustedFullPayment;
            $eassyCommission = $this->calculateEassyCommission($summary['eassy'], $memberId);
            $summary['eassy提成'] = $eassyCommission;
            $summary['总提成'] += $eassyCommission;
        }
        // SCOPUS检索
        else {
            $summary['scopus检索'] += $adjustedFullPayment;
            $newCommissionRate = $this->xuzhoucalculateCommission($summary['scopus检索']);
            $scopusCommission = $summary['scopus检索'] * $newCommissionRate;
            $summary['scopus检索提成'] = $scopusCommission;
            $summary['总提成'] += $scopusCommission;
        }
    
        // 计算多收部分的提成
        $payMoreCommission = $payMore * 0.20;
        $summary['总提成'] += $payMoreCommission;
    }

    private function calculateEassyCommission($eassyAmount, $memberId)
    {
        $commission = 0;
    
        // 特殊人的提成计算
        if ($memberId == '吕士钦') {
            return $eassyAmount * 0.15;
        } elseif ($memberId == '胡滨涛' && $eassyAmount > 50000) {
            return $eassyAmount * 0.10;
        }
    
        // 普通人的阶梯式提成计算
        if ($eassyAmount <= 20000) {
            $commission += $eassyAmount * 0.05;
        } else {
            $commission += 20000 * 0.05;
            $eassyAmount -= 20000;
    
            // 2-5万的提成计算
            if ($eassyAmount <= 30000) {
                $commission += $eassyAmount * 0.07;
            } else {
                $commission += 30000 * 0.07;
                $eassyAmount -= 30000;
    
                // 5万以上的提成计算
                if ($eassyAmount > 0) {
                    $commission += $eassyAmount * 0.10;
                }
            }
        }
    
        return $commission;
    }
}
