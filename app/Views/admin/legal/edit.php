<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-edit me-2"></i><?= esc($typeLabel) ?></h1>
        <p><?= esc($language['name']) ?> (<?= esc($language['code']) ?>)</p>
    </div>
    <a href="/admin/legal" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<form action="/admin/legal/save/<?= esc($type) ?>/<?= esc($language['code']) ?>" method="post">
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Title</label>
                <input type="text" name="title" class="form-control form-control-lg"
                    value="<?= esc($page['title'] ?? $typeLabel) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Content</label>
                <div id="toolbar" class="mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCmd('bold')"><i class="fas fa-bold"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCmd('italic')"><i class="fas fa-italic"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCmd('underline')"><i class="fas fa-underline"></i></button>
                    <span class="mx-1"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execFmt('h2')">H2</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execFmt('h3')">H3</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execFmt('p')">P</button>
                    <span class="mx-1"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCmd('insertUnorderedList')"><i class="fas fa-list-ul"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCmd('insertOrderedList')"><i class="fas fa-list-ol"></i></button>
                    <span class="mx-1"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSource()"><i class="fas fa-code"></i> HTML</button>
                </div>
                <div id="editor" contenteditable="true"
                    style="min-height: 400px; border: 1px solid #dee2e6; border-radius: 8px; padding: 16px; background: #fff; font-size: 15px; line-height: 1.7;"
                ><?= $page['content'] ?? '' ?></div>
                <textarea name="content" id="contentField" style="display:none;"></textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-green btn-lg" onclick="syncContent()">
            <i class="fas fa-save me-2"></i>Save
        </button>
        <a href="/admin/legal" class="btn btn-outline-secondary btn-lg">Cancel</a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function execCmd(cmd) { document.execCommand(cmd, false, null); }
    function execFmt(tag) { document.execCommand('formatBlock', false, '<' + tag + '>'); }

    function syncContent() {
        document.getElementById('contentField').value = document.getElementById('editor').innerHTML;
    }

    let sourceMode = false;
    function toggleSource() {
        const editor = document.getElementById('editor');
        if (sourceMode) {
            editor.innerHTML = editor.innerText;
            editor.contentEditable = true;
        } else {
            editor.innerText = editor.innerHTML;
            editor.contentEditable = true;
        }
        sourceMode = !sourceMode;
    }

    document.querySelector('form').addEventListener('submit', function() {
        if (sourceMode) {
            const editor = document.getElementById('editor');
            editor.innerHTML = editor.innerText;
            sourceMode = false;
        }
        syncContent();
    });
</script>
<?= $this->endSection() ?>
