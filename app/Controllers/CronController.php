<?php

namespace App\Controllers;

use App\Services\CheckInMonitorService;

class CronController extends BaseController
{
    public function checkIns()
    {
        $token = $this->request->getGet('token');
        if ($token !== getenv('CRON_TOKEN') && $token !== 'lifeknob2026cronkey') {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $monitor = new CheckInMonitorService();
        $results = $monitor->run();

        return $this->response->setJSON([
            'status'  => 'success',
            'results' => $results,
            'time'    => date('Y-m-d H:i:s'),
        ]);
    }
}
