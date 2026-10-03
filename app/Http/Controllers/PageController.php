<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        $set = array_filter(config('shop.contact'));

        // Nothing configured on a dev machine: show placeholder details (links go nowhere)
        // so the layout can be reviewed. Production only ever shows what is in .env.
        $dummy = $set === [] && app()->isLocal();
        $c = $dummy
            ? ['whatsapp' => '919876543210', 'instagram' => 'houseofthiraa', 'email' => 'hello@example.com']
            : $set;

        $channels = [];

        if (! empty($c['whatsapp'])) {
            $channels[] = [
                'label' => 'WhatsApp',
                'value' => $this->formatPhone($c['whatsapp']),
                'href' => $dummy ? '#' : "https://wa.me/{$c['whatsapp']}",
                'cta' => 'Message us',
            ];
        }
        if (! empty($c['instagram'])) {
            $channels[] = [
                'label' => 'Instagram',
                'value' => '@'.$c['instagram'],
                'href' => $dummy ? '#' : "https://instagram.com/{$c['instagram']}",
                'cta' => 'Follow along',
            ];
        }
        if (! empty($c['email'])) {
            $channels[] = [
                'label' => 'Email',
                'value' => $c['email'],
                'href' => $dummy ? '#' : "mailto:{$c['email']}",
                'cta' => 'Write to us',
            ];
        }

        return view('pages.contact', ['channels' => $channels, 'dummy' => $dummy]);
    }

    /** 919876543210 -> +91 98765 43210 */
    private function formatPhone(string $digits): string
    {
        return preg_match('/^91(\d{5})(\d{5})$/', $digits, $m) ? "+91 {$m[1]} {$m[2]}" : '+'.$digits;
    }

    public function policy()
    {
        return view('pages.policy');
    }
}
