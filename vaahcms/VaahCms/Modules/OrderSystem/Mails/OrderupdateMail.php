<?php  

namespace VaahCms\Modules\OrderSystem\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use VaahCms\Modules\OrderSystem\Models\Order;

class OrderupdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $changes;

    public function __construct(Order $order, array $changes)
    {
        $this->order = $order;
        $this->changes = $this->formatChanges($changes);
    }

    public function build()
    {
        return $this->view('ordersystem::emails.orderupdate')
            ->subject('Order Update - #' . $this->order->id)
            ->with([
                'order' => $this->order,
                'changes' => $this->changes,
            ]);
    }

    //  This formats each old/new value into a readable string
    private function formatValue($value)
    {
        if (is_array($value)) {
            if (isset($value['name'])) {
                return $value['name'];
            } elseif (isset($value['title'])) {
                return $value['title'];
            } else {
                return implode(', ', array_map(
                    fn($k, $v) => "$k: $v",
                    array_keys($value),
                    $value
                ));
            }
        }

        if (is_object($value)) {
            return method_exists($value, '__toString') ? (string) $value : 'Object';
        }

        return $value ?? '-';
    }

    private function formatChanges(array $changes)
    {
        // Define fields you want to ignore
        $excludeFields = ['created_by', 'updated_by'];

        $formatted = [];

        foreach ($changes as $field => $vals) {
            if (in_array($field, $excludeFields)) {
                continue; //  Skip unwanted fields
            }

            $formatted[$field] = [
                'old' => $this->formatValue($vals['old']),
                'new' => $this->formatValue($vals['new']),
            ];
        }

        return $formatted;
    }

}
