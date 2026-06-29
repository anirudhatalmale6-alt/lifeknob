<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LegalController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');

        $pages = $this->db->table('legal_pages')->orderBy('page_type')->orderBy('lang_code')->get()->getResultArray();

        $languages = $this->db->table('languages')->where('is_active', 1)->orderBy('sort_order')->get()->getResultArray();

        $grouped = [];
        foreach ($pages as $p) {
            $grouped[$p['page_type']][$p['lang_code']] = $p;
        }

        return view('admin/legal/index', [
            'activeMenu' => 'legal',
            'grouped' => $grouped,
            'languages' => $languages,
        ]);
    }

    public function edit($type = null, $code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$type || !$code) return redirect()->to('/admin/legal');

        $language = $this->db->table('languages')->where('code', $code)->get()->getRowArray();
        if (!$language) return redirect()->to('/admin/legal');

        $page = $this->db->table('legal_pages')
            ->where('page_type', $type)
            ->where('lang_code', $code)
            ->get()->getRowArray();

        $typeLabel = $type === 'tcs' ? 'Terms & Conditions' : 'Privacy Policy';

        return view('admin/legal/edit', [
            'activeMenu' => 'legal',
            'page' => $page,
            'type' => $type,
            'typeLabel' => $typeLabel,
            'language' => $language,
        ]);
    }

    public function save($type = null, $code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$type || !$code) return redirect()->to('/admin/legal');

        $title = trim($this->request->getPost('title'));
        $content = $this->request->getPost('content');

        if (empty($title)) {
            return redirect()->back()->with('error', 'Title is required');
        }

        $existing = $this->db->table('legal_pages')
            ->where('page_type', $type)
            ->where('lang_code', $code)
            ->get()->getRow();

        if ($existing) {
            $this->db->table('legal_pages')
                ->where('id', $existing->id)
                ->update(['title' => $title, 'content' => $content]);
        } else {
            $this->db->table('legal_pages')->insert([
                'page_type' => $type,
                'lang_code' => $code,
                'title' => $title,
                'content' => $content,
            ]);
        }

        $typeLabel = $type === 'tcs' ? 'T&C' : 'Privacy Policy';
        return redirect()->to("/admin/legal/edit/{$type}/{$code}")
            ->with('success', "{$typeLabel} saved for " . strtoupper($code));
    }
}
