<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContentBlock $contentBlock
 */

$this->assign('title', 'Edit Content Block - Content Blocks');
$this->layout = 'admin';

$this->Html->script('ContentBlocks.ckeditor/ckeditor', ['block' => true]);
$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);
?>

<style>
    .ck-editor__editable_inline {
        min-height: 25rem;
    }

    .content-blocks-edit-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .content-blocks-edit-header {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .content-blocks--form-heading {
        color: #2c3e50;
        font-weight: 600;
        margin: 0 0 1rem 0;
        font-size: 1.5rem;
    }

    .content-blocks--form-description {
        color: #6c757d;
        font-size: 1rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .content-blocks-edit-form {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 2rem;
        border: 1px solid #e9ecef;
    }

    .content-blocks--form-actions {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-secondary {
        background: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }

    .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .content-blocks--image-preview {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.2s ease;
    }

    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
</style>

<div class="content-blocks-edit-container">
    <div class="content-blocks-edit-header">
        <h3 class="content-blocks--form-heading"><?= $contentBlock->label ?></h3>
        <div class="content-blocks--form-description">
            <?= $contentBlock->description ?>
        </div>
    </div>

    <div class="content-blocks-edit-form">
        <?= $this->Form->create($contentBlock, ['type' => 'file']) ?>

        <?php
        if ($contentBlock->type === 'text') {
            echo $this->Form->control('value', [
                'type' => 'text',
                'value' => html_entity_decode($contentBlock->value),
                'label' => false,
                'class' => 'form-control'
            ]);
        } else if ($contentBlock->type === 'html') {
            echo $this->Form->control('value', [
                'type' => 'textarea',
                'label' => false,
                'id' => 'content-value-input',
                'class' => 'form-control'
            ]);
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", (event) => {
                    CKSource.Editor.create(
                        document.getElementById('content-value-input'),
                        {
                            toolbar: [
                                "heading", "|",
                                "bold", "italic", "underline", "|",
                                "bulletedList", "numberedList", "|",
                                "alignment", "blockQuote", "|",
                                "indent", "outdent", "|",
                                "link", "|",
                                "insertTable", "imageInsert", "mediaEmbed", "horizontalLine", "|",
                                "removeFormat", "|",
                                "sourceEditing", "|",
                                "undo", "redo",
                            ],
                            simpleUpload: {
                                uploadUrl: <?= json_encode($this->Url->build(['action' => 'upload'])) ?>,
                                headers: {
                                    'X-CSRF-TOKEN': <?= json_encode($this->request->getAttribute('csrfToken')) ?>,
                                }
                            }
                        }
                    ).then(editor => {
                        console.log(Array.from(editor.ui.componentFactory.names()));
                    });
                });
            </script>
            <?php
        } else if ($contentBlock->type === 'image') {
            if ($contentBlock->value) {
                echo $this->Html->image($contentBlock->value, ['class' => 'content-blocks--image-preview']);
            }

            echo $this->Form->control('value', [
                'type' => 'file',
                'accept' => 'image/*',
                'label' => false,
                'class' => 'form-control'
            ]);
        }
        ?>

        <div class="content-blocks--form-actions">
            <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Html->link('Cancel', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

