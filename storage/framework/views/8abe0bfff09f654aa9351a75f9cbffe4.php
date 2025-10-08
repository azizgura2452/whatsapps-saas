

<?php $__env->startSection('title'); ?>
    New Broadcast - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '<?php echo e(__('New Broadcast')); ?>' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('New Broadcast')); ?></h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="<?php echo e(route('admin.dashboard')); ?>">
                                <?php echo e(__('Home')); ?>

                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="<?php echo e(route('admin.broadcasts.index')); ?>">
                                <?php echo e(__('Broadcasts')); ?>

                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            <?php echo e(__('New Broadcast')); ?>

                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <form action="<?php echo e(route('admin.broadcasts.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="space-y-6">
                            <div>
                                <label for="whatsapp_template_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    <?php echo e(__('Title')); ?>

                                </label>
                                <input type="text" name="whatsapp_template_name" id="whatsapp_template_name" required 
                                       value="<?php echo e(old('whatsapp_template_name')); ?>" 
                                       placeholder="<?php echo e(__('Enter broadcast title')); ?>" 
                                       class="form-input form-control">
                            </div>

                            <div>
                                <label for="whatsapp_template_id" class="block text-sm font-medium">
                                    <?php echo e(__('Template')); ?>

                                </label>
                                <select name="whatsapp_template_id" id="whatsapp_template_id" class="form-control" required>
                                    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($template['name']); ?>"
                                                data-message="<?php echo e(htmlentities(json_encode($template['components']))); ?>">
                                            <?php echo e($template['name']); ?> (<?php echo e($template['language']); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div id="dynamicInputsContainer" class="space-y-4"></div>
                            <input type="hidden" name="custom_template" id="resolved_template_values">

                            <div>
                                <label class="block text-sm font-medium"><?php echo e(__('Preview')); ?></label>
                                <div id="template_preview" class="p-4 bg-gray-100 dark:bg-gray-800 border rounded text-sm whitespace-pre-wrap"></div>
                            </div>

                            
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <h3 class="font-medium mb-3"><?php echo e(__('Select Recipients')); ?></h3>
                                
                                <div class="space-y-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="recipient_source" value="all" 
                                               class="form-radio" checked onchange="toggleRecipientInputs()">
                                        <span><?php echo e(__('All Customers')); ?></span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="recipient_source" value="group" 
                                               class="form-radio" onchange="toggleRecipientInputs()">
                                        <span><?php echo e(__('Broadcast Group')); ?></span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="recipient_source" value="custom" 
                                               class="form-radio" onchange="toggleRecipientInputs()">
                                        <span><?php echo e(__('Custom Numbers')); ?></span>
                                    </label>
                                </div>

                                
                                <div id="group_selection" class="mt-4 hidden">
                                    <label class="block text-sm font-medium mb-2"><?php echo e(__('Select Group')); ?></label>
                                    <select name="broadcast_group_id" class="form-control">
                                        <option value=""><?php echo e(__('-- Select a group --')); ?></option>
                                        <?php $__currentLoopData = $broadcastGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($group->id); ?>">
                                                <?php echo e($group->name); ?> (<?php echo e($group->getCustomerCount()); ?> customers)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div id="custom_numbers" class="mt-4 hidden">
                                    <label class="block text-sm font-medium mb-2">
                                        <?php echo e(__('Phone Numbers (comma separated)')); ?>

                                    </label>
                                    <textarea name="custom_recipients" class="form-control" rows="3" 
                                              placeholder="+1234567890, +0987654321, ..."><?php echo e(old('custom_recipients')); ?></textarea>
                                    <small class="text-gray-500 dark:text-gray-400">
                                        <?php echo e(__('Enter phone numbers separated by commas')); ?>

                                    </small>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="submit" class="btn-primary"><?php echo e(__('Send Broadcast')); ?></button>
                                <a href="<?php echo e(route('admin.broadcasts.index')); ?>" class="btn-default"><?php echo e(__('Cancel')); ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script>
    function toggleRecipientInputs() {
        const source = document.querySelector('input[name="recipient_source"]:checked').value;
        document.getElementById('group_selection').classList.toggle('hidden', source !== 'group');
        document.getElementById('custom_numbers').classList.toggle('hidden', source !== 'custom');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const templateSelect = document.getElementById('whatsapp_template_id');
        const previewDiv = document.getElementById('template_preview');
        const dynamicInputsContainer = document.getElementById('dynamicInputsContainer');
        const resolvedInput = document.getElementById('resolved_template_values');

        function decodeHTMLEntities(str) {
            const textarea = document.createElement('textarea');
            textarea.innerHTML = str;
            return textarea.value;
        }

        function extractPlaceholders(componentsJson) {
            try {
                const components = JSON.parse(decodeHTMLEntities(componentsJson));
                const paramPlaceholders = [];

                components.forEach(component => {
                    component.example?.header_text_named_params?.forEach(param => {
                        if (param?.param_name) paramPlaceholders.push(param.param_name);
                    });
                    component.example?.body_text_named_params?.forEach(param => {
                        if (param?.param_name) paramPlaceholders.push(param.param_name);
                    });
                });

                return paramPlaceholders;
            } catch (e) {
                console.error('extractPlaceholders error:', e);
                return [];
            }
        }

        function renderPreview(componentsJson) {
            try {
                const components = JSON.parse(decodeHTMLEntities(componentsJson));
                let preview = '';

                components.forEach(component => {
                    if (component.type === 'HEADER' && component.format === 'TEXT') {
                        let headerText = component.text;
                        component.example?.header_text_named_params?.forEach(param => {
                            const pattern = `{{${param.param_name}}}`;
                            headerText = headerText.replace(pattern, param.example);
                        });
                        preview += `*${headerText}*\n\n`;
                    }

                    if (component.type === 'BODY') {
                        let bodyText = component.text;
                        component.example?.body_text_named_params?.forEach(param => {
                            const pattern = `{{${param.param_name}}}`;
                            bodyText = bodyText.replace(pattern, param.example);
                        });
                        preview += `${bodyText}\n\n`;
                    }

                    if (component.type === 'FOOTER') {
                        preview += `_${component.text}_\n\n`;
                    }

                    if (component.type === 'BUTTONS' && Array.isArray(component.buttons)) {
                        preview += '🔘 Buttons:\n';
                        component.buttons.forEach(button => {
                            if (button.type === 'COPY_CODE') {
                                preview += `- 📋 ${button.text} (copies: ${button.example?.[0] || 'CODE'})\n`;
                            } else if (button.type === 'URL') {
                                preview += `- 🌐 ${button.text}: ${button.url}\n`;
                            } else {
                                preview += `- 🔘 ${button.text}\n`;
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

        function updateTemplateUI() {
            const selectedOption = templateSelect.options[templateSelect.selectedIndex];
            if (!selectedOption) return;

            const messageJson = selectedOption.getAttribute('data-message') || '';
            const placeholders = extractPlaceholders(messageJson);
            const previewText = renderPreview(messageJson);

            previewDiv.textContent = previewText;
            dynamicInputsContainer.innerHTML = '';
            resolvedInput.value = '';

            if (placeholders.length) {
                placeholders.forEach((placeholder, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'space-y-1';

                    const label = document.createElement('label');
                    label.className = 'block text-sm font-medium text-gray-700 dark:text-gray-400';
                    label.textContent = placeholder;

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    input.placeholder = placeholder;
                    input.dataset.index = index;

                    input.addEventListener('input', () => {
                        const allInputs = dynamicInputsContainer.querySelectorAll('input');
                        const values = Array.from(allInputs).map(inp => inp.value.trim());
                        resolvedInput.value = values.join('~');
                    });

                    wrapper.appendChild(label);
                    wrapper.appendChild(input);
                    dynamicInputsContainer.appendChild(wrapper);
                });
            }
        }

        templateSelect.addEventListener('change', updateTemplateUI);
        updateTemplateUI();
    });
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/broadcasts/create.blade.php ENDPATH**/ ?>