<?php

namespace App\Services;

use App\Models\Order;

class VietQrService
{
    public function isConfigured(): bool
    {
        return filled($this->bankBin())
            && filled($this->accountNumber())
            && $this->accountNumber() !== 'your_bank_account_number';
    }

    public function accountInfo(): array
    {
        return [
            'bank_bin' => $this->bankBin(),
            'account_number' => $this->accountNumber(),
            'account_name' => config('services.vietqr.account_name'),
        ];
    }

    public function paymentContent(Order $order): string
    {
        return 'MEALKIT DH' . $order->id;
    }

    public function qrUrl(Order $order): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $path = sprintf(
            '%s/%s-%s-%s.png',
            rtrim((string) config('services.vietqr.image_base_url'), '/'),
            rawurlencode((string) $this->bankBin()),
            rawurlencode((string) $this->accountNumber()),
            rawurlencode((string) config('services.vietqr.template', 'compact2'))
        );

        $query = http_build_query([
            'amount' => (int) round($order->total_price),
            'addInfo' => $this->paymentContent($order),
            'accountName' => config('services.vietqr.account_name'),
        ]);

        return $path . '?' . $query;
    }

    private function bankBin(): ?string
    {
        return config('services.vietqr.bank_bin');
    }

    private function accountNumber(): ?string
    {
        return config('services.vietqr.account_number');
    }
}
