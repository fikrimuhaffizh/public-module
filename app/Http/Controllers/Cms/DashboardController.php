<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\Services\CmsService;

class DashboardController extends Controller
{
    public function __construct(protected CmsService $cmsService) {}

    public function index()
    {
        $dashboardData = $this->cmsService->getDashboardStats();

        $slideshows = $dashboardData['slideshows'];
        $recentNews = $dashboardData['recent_news'];
        $recentAnnouncements = $dashboardData['recent_announcements'];

        $stats = [
            'total_slideshows' => $dashboardData['total_slideshows'],
            'total_announcements' => $dashboardData['total_announcements'],
            'total_news' => $dashboardData['total_news'],
            'total_pages' => $dashboardData['total_pages'],
            'total_menus' => $dashboardData['total_menus'],
        ];

        return view('public::pages.cms.dashboard', compact('slideshows', 'recentNews', 'recentAnnouncements', 'stats'));
    }
}
