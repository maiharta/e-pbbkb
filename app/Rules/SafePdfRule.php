<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class SafePdfRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || !$value->isValid()) {
            $fail('File tidak valid.');
            return;
        }

        // Check if it's actually a PDF file
        if ($value->getMimeType() !== 'application/pdf') {
            $fail('File harus berupa PDF.');
            return;
        }

        try {
            $content = file_get_contents($value->getRealPath());
            
            // Verify PDF header
            if (strpos($content, '%PDF-') !== 0) {
                $fail('File PDF tidak valid.');
                return;
            }

            // Dangerous patterns in PDF that could contain JavaScript or malicious content
            $dangerousPatterns = [
                '/\/JavaScript/i',           // JavaScript object
                '/\/JS/i',                   // JS object (short form)
                '/\/Action/i',               // Action objects
                '/\/OpenAction/i',           // Open action
                '/\/Launch/i',               // Launch action
                '/\/EmbeddedFile/i',         // Embedded files
                '/\/FileAttachment/i',       // File attachments
                '/\/SubmitForm/i',           // Form submission
                '/\/ImportData/i',           // Data import
                '/\/GoToR/i',                // Remote goto
                '/\/Sound/i',                // Sound objects
                '/\/Movie/i',                // Movie objects
                '/\/RichMedia/i',            // Rich media
                '/\/3D/i',                   // 3D objects
                '/\/XFA/i',                  // XFA forms
                '/app\.alert/i',             // JavaScript alert
                '/app\.launchURL/i',         // Launch URL
                '/this\.print/i',            // Print function
                '/this\.submitForm/i',       // Submit form
                '/eval\s*\(/i',              // JavaScript eval
                '/unescape\s*\(/i',          // JavaScript unescape
                '/String\.fromCharCode/i',   // String encoding
                '/document\.write/i',        // Document write
                '/window\.open/i',           // Window open
                '/XMLHttpRequest/i',         // AJAX requests
            ];

            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    Log::warning('Dangerous PDF content detected', [
                        'pattern' => $pattern,
                        'file' => $value->getClientOriginalName(),
                        'user_id' => auth()->id() ?? 'unknown'
                    ]);
                    $fail('PDF mengandung konten berbahaya (JavaScript atau konten aktif lainnya) dan tidak dapat diunggah.');
                    return;
                }
            }

            // Check for suspicious byte sequences that might indicate obfuscated JavaScript
            $suspiciousBytes = [
                '\x2F\x4A\x53',              // /JS in hex
                '\x2F\x4A\x61\x76\x61\x53\x63\x72\x69\x70\x74', // /JavaScript in hex
            ];

            foreach ($suspiciousBytes as $bytes) {
                if (strpos($content, $bytes) !== false) {
                    Log::warning('Suspicious byte sequence detected in PDF', [
                        'sequence' => $bytes,
                        'file' => $value->getClientOriginalName(),
                        'user_id' => auth()->id() ?? 'unknown'
                    ]);
                    $fail('PDF mengandung konten mencurigakan dan tidak dapat diunggah.');
                    return;
                }
            }

        } catch (\Exception $e) {
            Log::error('Error validating PDF safety: ' . $e->getMessage(), [
                'file' => $value->getClientOriginalName(),
                'user_id' => auth()->id() ?? 'unknown'
            ]);
            $fail('Terjadi kesalahan saat memvalidasi file PDF.');
        }
    }
}
