

<?php $__env->startSection('title'); ?>
    New Broadcast - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-6 max-w-4xl mx-auto">
        <h1 class="text-xl font-semibold mb-4">New Broadcast</h1>

        <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <form action="<?php echo e(route('admin.broadcasts.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="space-y-6">
                <div>
                    <label for="whatsapp_template_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Title')); ?></label>
                    <input type="text" name="whatsapp_template_name" id="whatsapp_template_name" required value="<?php echo e(old('whatsapp_template_name')); ?>" placeholder="<?php echo e(__('Enter broadcast title')); ?>" class="form-input form-control">
                </div>
                <div>
                    <label for="whatsapp_template_id" class="block text-sm font-medium">Template</label>
                    <select name="whatsapp_template_id" id="whatsapp_template_id" class="form-control" required>
                        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($template['name']); ?>"
                                    data-message="<?php echo e(htmlentities(json_encode($template['components']))); ?>">
                                <?php echo e($template['name']); ?> (<?php echo e($template['language']); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium" for="custom_template">Custom Message</label>
                    <textarea name="custom_template" id="custom_template" class="form-control" rows="6" required style="min-height: 100px"><?php echo e(old('custom_template')); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium">Preview</label>
                    <div id="template_preview" class="p-4 bg-gray-100 border rounded text-sm whitespace-pre-wrap"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Other Numbers (comma separated)</label>
                    <textarea name="custom_recipients" class="form-control" rows="3"><?php echo e(old('custom_recipients')); ?></textarea>
                    <small class="text-gray-500">Leave blank to send to all customers</small>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="btn-primary">Save</button>
                    <a href="<?php echo e(route('admin.broadcasts.index')); ?>" class="btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var templateSelect = document.getElementById('whatsapp_template_id');
        var templateContent = document.getElementById('custom_template');
        var previewDiv = document.getElementById('template_preview');

        function decodeHTMLEntities(str) {
            var textarea = document.createElement('textarea');
            textarea.innerHTML = str;
            return textarea.value;
        }

        function extractInputFormat(componentsJson) {
            try {
                var components = JSON.parse(decodeHTMLEntities(componentsJson));
                var paramPlaceholders = [];

                components.forEach(function (component) {
                    if (component.example && component.example.header_text_named_params) {
                        component.example.header_text_named_params.forEach(function (param) {
                            if (param && typeof param.param_name === 'string') {
                                paramPlaceholders.push(param.param_name);
                            }
                        });
                    }

                    if (component.example && component.example.body_text_named_params) {
                        component.example.body_text_named_params.forEach(function (param) {
                            if (param && typeof param.param_name === 'string') {
                                paramPlaceholders.push(param.param_name);
                            }
                        });
                    }
                });

                return paramPlaceholders.join('~');
            } catch (e) {
                console.error('extractInputFormat error:', e);
                return '';
            }
        }

        function renderPreview(componentsJson) {
            try {
                var components = JSON.parse(decodeHTMLEntities(componentsJson));
                var preview = '';

                components.forEach(function (component) {
                    if (component.type === 'HEADER' && component.format === 'TEXT') {
                        var headerText = component.text;
                        if (component.example && component.example.header_text_named_params) {
                            component.example.header_text_named_params.forEach(function (param) {
                                if (param && typeof param.param_name === 'string') {
                                    var pattern = '<?php echo e(' + param.param_name + '); ?>';
                                    headerText = headerText.replace(pattern, param.example);
                                }
                            });
                        }
                        preview += '*' + headerText + '*\n\n';
                    }

                    if (component.type === 'BODY') {
                        var bodyText = component.text;
                        if (component.example && component.example.body_text_named_params) {
                            component.example.body_text_named_params.forEach(function (param) {
                                if (param && typeof param.param_name === 'string') {
                                    var pattern = '<?php echo e(' + param.param_name + '); ?>';
                                    bodyText = bodyText.replace(pattern, param.example);
                                }
                            });
                        }
                        preview += bodyText + '\n\n';
                    }

                    if (component.type === 'FOOTER') {
                        preview += '_' + component.text + '_\n\n';
                    }

                    if (component.type === 'BUTTONS' && Array.isArray(component.buttons)) {
                        preview += '🔘 Buttons:\n';
                        component.buttons.forEach(function (button) {
                            if (button.type === 'COPY_CODE') {
                                preview += '- 📋 ' + button.text + ' (copies: ' + (button.example && button.example[0] ? button.example[0] : 'CODE') + ')\n';
                            } else if (button.type === 'URL') {
                                preview += '- 🌐 ' + button.text + ': ' + button.url + '\n';
                            } else {
                                preview += '- 🔘 ' + button.text + '\n';
                            }
                        });
                        preview += '\n';
                    }
                });

                return preview.trim();
            } catch (e) {
                console.error('renderPreview error:', e);
                return '⚠️ Invalid template format.';
            }
        }

        function updateTemplateAndPreview() {
            var selectedOption = templateSelect.options[templateSelect.selectedIndex];
            if (!selectedOption) return;

            var messageJson = selectedOption.getAttribute('data-message') || '';

            // Populate input format
            var inputFormat = extractInputFormat(messageJson);
            templateContent.value = inputFormat;

            // Show preview
            var previewText = renderPreview(messageJson);
            previewDiv.textContent = previewText;
        }

        templateSelect.addEventListener('change', updateTemplateAndPreview);

        // Initial render on load
        updateTemplateAndPreview();
    });
</script>

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/broadcasts/create.blade.php ENDPATH**/ ?>