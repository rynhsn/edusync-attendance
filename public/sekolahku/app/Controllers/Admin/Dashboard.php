<?php

namespace App\Controllers\Admin;

use App\Models\NewsModel;
use App\Models\ContactMessageModel;
use App\Models\NewsCommentModel;
use App\Models\TeacherModel;
use App\Models\ProgramModel;
use App\Models\EventModel;
use App\Models\AchievementModel;
use App\Models\ExtracurricularModel;
use App\Models\GalleryModel;
use App\Models\FaqModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    /**
     * Display the admin dashboard with summary statistics and recent data.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Dashboard';

        $this->data['total_news']           = (new NewsModel())->countAll();
        $this->data['total_events']         = (new EventModel())->countAll();
        $this->data['total_teachers']       = (new TeacherModel())->countAll();
        $this->data['total_programs']       = (new ProgramModel())->countAll();
        $this->data['total_messages']       = (new ContactMessageModel())->getUnreadCount();
        $this->data['total_comments']       = (new NewsCommentModel())->getPendingCount();
        $this->data['total_achievements']   = (new AchievementModel())->countAll();
        $this->data['total_extracurriculars'] = (new ExtracurricularModel())->countAll();
        $this->data['total_galleries']      = (new GalleryModel())->countAll();
        $this->data['total_faqs']           = (new FaqModel())->countAll();
        $this->data['total_users']          = (new UserModel())->countAll();

        $this->data['recent_news']       = (new NewsModel())->getRecent(5);
        $this->data['upcoming_events']   = (new EventModel())->getUpcoming(5);
        $this->data['recent_messages']   = (new ContactMessageModel())->getRecent(5);
        $this->data['pending_comments']  = (new NewsCommentModel())->getPendingRecent(5);

        return view('admin/dashboard', $this->data);
    }
}
