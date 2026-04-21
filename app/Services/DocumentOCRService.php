<?php

namespace App\Services;

use App\Models\LoanDocument;
use Illuminate\Support\Facades\Storage;

class DocumentOCRService
{
    public function processDocument(LoanDocument $document): array
    {
        $document->update(['ocr_status' => 'processing']);

        try {
            $filePath = Storage::path($document->file_path);
            $fileExtension = pathinfo($document->file_path, PATHINFO_EXTENSION);

            // Extract text based on file type
            $extractedText = match (strtolower($fileExtension)) {
                'pdf' => $this->extractFromPDF($filePath),
                'jpg', 'jpeg', 'png' => $this->extractFromImage($filePath),
                default => '',
            };

            // Parse document based on type
            $parsedData = $this->parseDocument($extractedText, $document->document_type);

            // Verify authenticity
            $verification = $this->verifyDocument($parsedData, $document->document_type);

            $document->update([
                'ocr_status' => 'completed',
                'ocr_data' => [
                    'raw_text' => $extractedText,
                    'parsed_data' => $parsedData,
                    'verification' => $verification,
                ],
                'ocr_confidence' => $verification['confidence_score'],
            ]);

            return [
                'success' => true,
                'data' => $parsedData,
                'verification' => $verification,
            ];
        } catch (\Exception $e) {
            $document->update(['ocr_status' => 'failed']);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function extractFromPDF(string $filePath): string
    {
        // Integration with PDF parser library
        // Using pdftotext or similar
        $command = "pdftotext -layout " . escapeshellarg($filePath) . " -";
        return shell_exec($command) ?? '';
    }

    private function extractFromImage(string $filePath): string
    {
        // Integration with OCR engine (Tesseract or cloud API)
        // Placeholder for actual OCR implementation
        return '';
    }

    private function parseDocument(string $text, string $documentType): array
    {
        return match ($documentType) {
            'aadhaar' => $this->parseAadhaar($text),
            'pan' => $this->parsePAN($text),
            'passport' => $this->parsePassport($text),
            'driving_license' => $this->parseDrivingLicense($text),
            'bank_statement' => $this->parseBankStatement($text),
            'salary_slip' => $this->parseSalarySlip($text),
            'utility_bill' => $this->parseUtilityBill($text),
            default => ['raw_text' => $text],
        };
    }

    private function parseAadhaar(string $text): array
    {
        // Extract Aadhaar number (12 digits)
        preg_match('/(\d{4}\s?\d{4}\s?\d{4})/', $text, $matches);
        $aadhaarNumber = $matches[1] ?? null;

        // Extract name
        preg_match('/Name[:\s]+([A-Za-z\s]+)/i', $text, $nameMatch);
        $name = trim($nameMatch[1] ?? '');

        // Extract DOB
        preg_match('/(DOB|Date of Birth)[:\s]+(\d{2}\/\d{2}\/\d{4})/i', $text, $dobMatch);
        $dob = $dobMatch[2] ?? null;

        // Extract gender
        preg_match('/(Male|Female)/i', $text, $genderMatch);
        $gender = strtolower($genderMatch[1] ?? '');

        return [
            'document_type' => 'aadhaar',
            'number' => $aadhaarNumber ? str_replace(' ', '', $aadhaarNumber) : null,
            'name' => $name,
            'date_of_birth' => $dob,
            'gender' => $gender,
        ];
    }

    private function parsePAN(string $text): array
    {
        // Extract PAN number (AAAAA0000A format)
        preg_match('/([A-Z]{5}\d{4}[A-Z]{1})/', $text, $matches);
        $panNumber = $matches[1] ?? null;

        // Extract name
        preg_match('/Name[:\s]+([A-Za-z\s]+)/i', $text, $nameMatch);
        $name = trim($nameMatch[1] ?? '');

        // Extract father's name
        preg_match('/Father[^:]*[:\s]+([A-Za-z\s]+)/i', $text, $fatherMatch);
        $fatherName = trim($fatherMatch[1] ?? '');

        return [
            'document_type' => 'pan',
            'number' => $panNumber,
            'name' => $name,
            'father_name' => $fatherName,
        ];
    }

    private function parseBankStatement(string $text): array
    {
        // Extract transactions
        preg_match_all('/(\d{2}-\d{2}-\d{4})\s+(.+?)\s+([\d,]+\.\d{2})/', $text, $matches, PREG_SET_ORDER);

        $transactions = [];
        foreach ($matches as $match) {
            $transactions[] = [
                'date' => $match[1],
                'description' => trim($match[2]),
                'amount' => (float) str_replace(',', '', $match[3]),
            ];
        }

        // Extract account number
        preg_match('/Account\s*Number[:\s]+(\d+)/i', $text, $accMatch);
        $accountNumber = $accMatch[1] ?? null;

        // Extract bank name
        preg_match('/(HDFC|ICICI|SBI|Axis|Kotak)[\s\w]+Bank/i', $text, $bankMatch);
        $bankName = $bankMatch[0] ?? null;

        return [
            'document_type' => 'bank_statement',
            'account_number' => $accountNumber,
            'bank_name' => $bankName,
            'transactions' => $transactions,
            'transaction_count' => count($transactions),
        ];
    }

    private function parseSalarySlip(string $text): array
    {
        // Extract gross salary
        preg_match('/Gross\s*Salary[:\s]+Rs?[.\s]*([\d,]+)/i', $text, $grossMatch);
        $grossSalary = (float) str_replace(',', '', $grossMatch[1] ?? 0);

        // Extract net salary
        preg_match('/Net\s*(Salary|Pay)[:\s]+Rs?[.\s]*([\d,]+)/i', $text, $netMatch);
        $netSalary = (float) str_replace(',', '', $netMatch[2] ?? $netMatch[1] ?? 0);

        // Extract employer
        preg_match('/Employer[:\s]+([A-Za-z\s]+)/i', $text, $employerMatch);
        $employer = trim($employerMatch[1] ?? '');

        // Extract month
        preg_match('/(January|February|March|April|May|June|July|August|September|October|November|December)[\s\d]{4,}/i', $text, $monthMatch);
        $month = $monthMatch[0] ?? null;

        return [
            'document_type' => 'salary_slip',
            'gross_salary' => $grossSalary,
            'net_salary' => $netSalary,
            'employer' => $employer,
            'month' => $month,
        ];
    }

    private function verifyDocument(array $data, string $documentType): array
    {
        $checks = [];
        $confidenceScore = 0;

        // Check document number format
        if (! empty($data['number'])) {
            $checks['number_format'] = $this->validateDocumentNumber($data['number'], $documentType);
            $confidenceScore += $checks['number_format'] ? 30 : 0;
        }

        // Check required fields present
        $requiredFields = $this->getRequiredFields($documentType);
        $presentFields = count(array_filter(array_intersect_key($data, array_flip($requiredFields))));
        $checks['field_completeness'] = $presentFields / count($requiredFields);
        $confidenceScore += $checks['field_completeness'] * 40;

        // Check data consistency
        $checks['data_consistency'] = $this->checkDataConsistency($data);
        $confidenceScore += $checks['data_consistency'] ? 30 : 0;

        return [
            'confidence_score' => min($confidenceScore, 100),
            'checks' => $checks,
            'verified' => $confidenceScore >= 70,
        ];
    }

    private function validateDocumentNumber(string $number, string $type): bool
    {
        return match ($type) {
            'aadhaar' => preg_match('/^\d{12}$/', $number),
            'pan' => preg_match('/^[A-Z]{5}\d{4}[A-Z]$/', $number),
            default => strlen($number) >= 5,
        };
    }

    private function getRequiredFields(string $type): array
    {
        return match ($type) {
            'aadhaar' => ['number', 'name'],
            'pan' => ['number', 'name'],
            'bank_statement' => ['account_number', 'bank_name'],
            'salary_slip' => ['net_salary'],
            default => ['number'],
        };
    }

    private function checkDataConsistency(array $data): bool
    {
        // Check if data makes logical sense
        if (isset($data['date_of_birth'])) {
            $dob = strtotime($data['date_of_birth']);
            if ($dob && $dob > time()) {
                return false;
            }
        }

        return true;
    }
}
