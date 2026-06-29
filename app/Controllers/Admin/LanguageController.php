<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LanguageController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');

        $languages = $this->db->table('languages')->orderBy('sort_order')->get()->getResultArray();

        foreach ($languages as &$lang) {
            $lang['translation_count'] = $this->db->table('translations')
                ->where('lang_code', $lang['code'])->countAllResults();
        }

        $totalKeys = $this->db->table('translations')
            ->where('lang_code', 'en')->countAllResults();

        return view('admin/languages/index', [
            'activeMenu' => 'languages',
            'languages' => $languages,
            'totalKeys' => $totalKeys,
        ]);
    }

    public function edit($code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$code) return redirect()->to('/admin/languages');

        $language = $this->db->table('languages')->where('code', $code)->get()->getRowArray();
        if (!$language) return redirect()->to('/admin/languages');

        $enStrings = $this->db->table('translations')
            ->where('lang_code', 'en')
            ->orderBy('string_key')
            ->get()->getResultArray();

        $langStrings = [];
        $rows = $this->db->table('translations')
            ->where('lang_code', $code)
            ->get()->getResultArray();
        foreach ($rows as $r) {
            $langStrings[$r['string_key']] = $r['string_value'];
        }

        $grouped = [];
        foreach ($enStrings as $s) {
            $parts = explode('_', $s['string_key'], 2);
            $group = $parts[0];
            $grouped[$group][] = [
                'key' => $s['string_key'],
                'en_value' => $s['string_value'],
                'value' => $langStrings[$s['string_key']] ?? '',
            ];
        }

        return view('admin/languages/edit', [
            'activeMenu' => 'languages',
            'language' => $language,
            'grouped' => $grouped,
            'isDefault' => $code === 'en',
        ]);
    }

    public function save($code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$code) return redirect()->to('/admin/languages');

        $translations = $this->request->getPost('translations');
        if (!is_array($translations)) return redirect()->back()->with('error', 'No data received');

        $saved = 0;
        foreach ($translations as $key => $value) {
            $value = trim($value);
            if (empty($value)) {
                $this->db->table('translations')
                    ->where('lang_code', $code)
                    ->where('string_key', $key)
                    ->delete();
                continue;
            }

            $existing = $this->db->table('translations')
                ->where('lang_code', $code)
                ->where('string_key', $key)
                ->get()->getRow();

            if ($existing) {
                $this->db->table('translations')
                    ->where('id', $existing->id)
                    ->update(['string_value' => $value]);
            } else {
                $this->db->table('translations')->insert([
                    'lang_code' => $code,
                    'string_key' => $key,
                    'string_value' => $value,
                ]);
            }
            $saved++;
        }

        return redirect()->to("/admin/languages/edit/{$code}")->with('success', "Saved {$saved} translations for " . strtoupper($code));
    }

    public function addLanguage()
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');

        $code = strtolower(trim($this->request->getPost('code')));
        $name = trim($this->request->getPost('name'));

        if (empty($code) || empty($name)) {
            return redirect()->back()->with('error', 'Code and name are required');
        }

        if (strlen($code) > 10) {
            return redirect()->back()->with('error', 'Language code too long (max 10 chars)');
        }

        $existing = $this->db->table('languages')->where('code', $code)->get()->getRow();
        if ($existing) {
            return redirect()->back()->with('error', 'Language already exists');
        }

        $maxSort = $this->db->table('languages')->selectMax('sort_order')->get()->getRow()->sort_order ?? 0;

        $this->db->table('languages')->insert([
            'code' => $code,
            'name' => $name,
            'is_active' => 1,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->to('/admin/languages')->with('success', "Language '{$name}' added");
    }

    public function toggleLanguage($code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$code || $code === 'en') return redirect()->to('/admin/languages');

        $lang = $this->db->table('languages')->where('code', $code)->get()->getRow();
        if ($lang) {
            $this->db->table('languages')
                ->where('code', $code)
                ->update(['is_active' => $lang->is_active ? 0 : 1]);
        }

        return redirect()->to('/admin/languages')->with('success', 'Language status updated');
    }

    public function addKey()
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');

        $key = strtolower(trim($this->request->getPost('key')));
        $value = trim($this->request->getPost('value'));
        $returnTo = $this->request->getPost('return_to') ?? 'en';

        $key = preg_replace('/[^a-z0-9_]/', '_', $key);

        if (empty($key) || empty($value)) {
            return redirect()->back()->with('error', 'Key and English value are required');
        }

        $existing = $this->db->table('translations')
            ->where('lang_code', 'en')
            ->where('string_key', $key)
            ->get()->getRow();

        if ($existing) {
            return redirect()->back()->with('error', "Key '{$key}' already exists");
        }

        $this->db->table('translations')->insert([
            'lang_code' => 'en',
            'string_key' => $key,
            'string_value' => $value,
        ]);

        return redirect()->to("/admin/languages/edit/{$returnTo}")->with('success', "Key '{$key}' added");
    }

    public function exportCsv($code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$code) return redirect()->to('/admin/languages');

        $language = $this->db->table('languages')->where('code', $code)->get()->getRowArray();
        if (!$language) return redirect()->to('/admin/languages');

        $enStrings = $this->db->table('translations')
            ->where('lang_code', 'en')
            ->orderBy('string_key')
            ->get()->getResultArray();

        $langStrings = [];
        if ($code !== 'en') {
            $rows = $this->db->table('translations')
                ->where('lang_code', $code)
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $langStrings[$r['string_key']] = $r['string_value'];
            }
        }

        $filename = "lifeknob_translations_{$code}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['key', 'english', $language['name']]);

        foreach ($enStrings as $s) {
            $translation = ($code === 'en') ? $s['string_value'] : ($langStrings[$s['string_key']] ?? '');
            fputcsv($output, [$s['string_key'], $s['string_value'], $translation]);
        }

        fclose($output);
        exit;
    }

    public function importCsv($code = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$code) return redirect()->to('/admin/languages');

        $file = $this->request->getFile('csv_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid CSV file');
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return redirect()->back()->with('error', 'Only CSV/TXT files are accepted');
        }

        $content = file_get_contents($file->getTempName());
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = str_getcsv($content, "\n");

        $saved = 0;
        $skipped = 0;
        $headerSkipped = false;

        foreach ($lines as $line) {
            $cols = str_getcsv($line);
            if (count($cols) < 3) continue;

            if (!$headerSkipped && strtolower(trim($cols[0])) === 'key') {
                $headerSkipped = true;
                continue;
            }
            $headerSkipped = true;

            $key = trim($cols[0]);
            $translation = trim($cols[2]);

            if (empty($key) || empty($translation)) {
                $skipped++;
                continue;
            }

            $existing = $this->db->table('translations')
                ->where('lang_code', $code)
                ->where('string_key', $key)
                ->get()->getRow();

            if ($existing) {
                $this->db->table('translations')
                    ->where('id', $existing->id)
                    ->update(['string_value' => $translation]);
            } else {
                $this->db->table('translations')->insert([
                    'lang_code' => $code,
                    'string_key' => $key,
                    'string_value' => $translation,
                ]);
            }
            $saved++;
        }

        return redirect()->to("/admin/languages/edit/{$code}")
            ->with('success', "Imported {$saved} translations" . ($skipped > 0 ? " ({$skipped} skipped)" : ''));
    }
}
