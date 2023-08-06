<?php

namespace Bo\PageManager\App\Http\Controllers;
use App\Http\Controllers\Controller;
use Bo\PageManager\App\Models\Page;

class PageController extends Controller
{
    public function index($slug, $subs = null)
    {
        $page = Page::findBySlug($slug);
        if (!$page)
        {
            abort(404, 'Please go back to our <a href="'.url('').'">homepage</a>.');
        }

        $this->data['title'] = $page->title;
        $this->data['page'] = $page->withFakes();

        return view('pagemanager::page.' . $page->template , $this->data);
    }
}
