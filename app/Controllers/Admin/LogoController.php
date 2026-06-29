<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LogoController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');

        $logos = $this->db->table('app_logos')->orderBy('id')->get()->getResultArray();

        return view('admin/logos/index', [
            'activeMenu' => 'logos',
            'logos' => $logos,
        ]);
    }

    public function upload($key = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$key) return redirect()->to('/admin/logos');

        $logo = $this->db->table('app_logos')->where('logo_key', $key)->get()->getRow();
        if (!$logo) return redirect()->to('/admin/logos')->with('error', 'Invalid logo key');

        $file = $this->request->getFile('logo_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid file');
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['svg', 'png', 'jpg', 'jpeg', 'webp'])) {
            return redirect()->back()->with('error', 'Only SVG, PNG, JPG, WebP files are accepted');
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->back()->with('error', 'File must be under 2MB');
        }

        $uploadPath = FCPATH . 'uploads/logos/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($logo->file_path) {
            $oldFile = FCPATH . ltrim($logo->file_path, '/');
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $newName = 'logo_' . $key . '_' . time() . '.' . $ext;
        $file->move($uploadPath, $newName);

        $filePath = '/uploads/logos/' . $newName;
        $this->db->table('app_logos')
            ->where('logo_key', $key)
            ->update(['file_path' => $filePath]);

        return redirect()->to('/admin/logos')->with('success', "Logo '{$logo->label}' uploaded successfully");
    }

    public function delete($key = null)
    {
        if (!session()->get('is_admin')) return redirect()->to('/admin/login');
        if (!$key) return redirect()->to('/admin/logos');

        $logo = $this->db->table('app_logos')->where('logo_key', $key)->get()->getRow();
        if (!$logo) return redirect()->to('/admin/logos');

        if ($logo->file_path) {
            $oldFile = FCPATH . ltrim($logo->file_path, '/');
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $this->db->table('app_logos')
                ->where('logo_key', $key)
                ->update(['file_path' => null]);
        }

        return redirect()->to('/admin/logos')->with('success', 'Logo removed');
    }
}
