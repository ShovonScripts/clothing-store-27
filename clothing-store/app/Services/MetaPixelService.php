<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MetaPixelService
{
    public function getPixelId(): ?string
    {
        $id = settings('meta_pixel_id');

        return $id ?: null;
    }

    public function getPurchaseTrigger(): string
    {
        return settings('pixel_purchase_on', 'approved');
    }

    public function isEnabled(): bool
    {
        return ! empty($this->getPixelId());
    }

    public function getBaseScript(): string
    {
        $pixelId = $this->getPixelId();
        if (! $pixelId) {
            return '';
        }

        $eventId = Str::uuid()->toString();
        View::share('metaPixelEventId', $eventId);

        return <<<JS
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{$pixelId}');
  fbq('track', 'PageView', {}, {eventID: '{$eventId}'});
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"
/></noscript>
JS;
    }

    public function getEventScript(string $eventName, array $params = [], ?string $eventId = null): string
    {
        $pixelId = $this->getPixelId();
        if (! $pixelId) {
            return '';
        }

        $eventId = $eventId ?? Str::uuid()->toString();
        $paramsJson = json_encode($params);

        return <<<JS
<script>
  fbq('track', '{$eventName}', {$paramsJson}, {eventID: '{$eventId}'});
</script>
JS;
    }

    public function getViewContentScript(array $productData, ?string $eventId = null): string
    {
        return $this->getEventScript('ViewContent', [
            'content_type' => 'product',
            'content_ids' => [$productData['id'] ?? ''],
            'content_name' => $productData['name'] ?? '',
            'content_category' => $productData['category'] ?? '',
            'value' => ($productData['price'] ?? 0) / 100,
            'currency' => 'BDT',
        ], $eventId);
    }

    public function getAddToCartScript(array $productData, int $quantity, ?string $eventId = null): string
    {
        return $this->getEventScript('AddToCart', [
            'content_type' => 'product',
            'content_ids' => [$productData['id'] ?? ''],
            'content_name' => $productData['name'] ?? '',
            'content_category' => $productData['category'] ?? '',
            'value' => (($productData['price'] ?? 0) * $quantity) / 100,
            'currency' => 'BDT',
        ], $eventId);
    }

    public function getInitiateCheckoutScript(array $params, ?string $eventId = null): string
    {
        return $this->getEventScript('InitiateCheckout', $params, $eventId);
    }

    public function getAddPaymentInfoScript(array $params, ?string $eventId = null): string
    {
        return $this->getEventScript('AddPaymentInfo', $params, $eventId);
    }

    public function getLeadScript(array $params, ?string $eventId = null): string
    {
        return $this->getEventScript('Lead', $params, $eventId);
    }

    public function getPurchaseScript(array $params, ?string $eventId = null): string
    {
        return $this->getEventScript('Purchase', $params, $eventId);
    }
}
