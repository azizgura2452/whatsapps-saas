

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Create Flow Step')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-4xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            <?php echo e(__('Create Flow Step')); ?>

        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                        <?php echo e(__('Home')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.flow-builder.index')); ?>">
                        <?php echo e(__('Flow Builder')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Create')); ?></li>
            </ol>
        </nav>
    </div>

    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="<?php echo e(route('admin.flow-builder.store')); ?>" 
              method="POST" 
              x-data="flowStepForm()">
            <?php echo csrf_field(); ?>

            <!-- Basic Information -->
            <div class="space-y-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo e(__('Basic Information')); ?></h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        <?php echo e(__('Step Name')); ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           value="<?php echo e(old('name')); ?>"
                           placeholder="e.g., Welcome Message"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        <?php echo e(__('Step Type')); ?> <span class="text-red-500">*</span>
                    </label>
                    <select name="step_type" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                        <?php $__currentLoopData = $stepTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('step_type') == $value ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['step_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        <?php echo e(__('Order')); ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="order" required min="0"
                           value="<?php echo e(old('order', 0)); ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                    <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-400">
                        <?php echo e(__('Active')); ?>

                    </label>
                </div>
            </div>

            <!-- Messages Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <?php echo e(__('Messages')); ?> <span class="text-red-500">*</span>
                    </h3>
                    <button type="button" @click="addMessage()" 
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        <i class="bi bi-plus-circle mr-1"></i>
                        <?php echo e(__('Add Message')); ?>

                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(message, index) in messages" :key="index">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
                            <div class="flex justify-between items-start mb-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300" 
                                      x-text="'Message ' + (index + 1)"></span>
                                <button type="button" @click="removeMessage(index)"
                                        class="text-red-600 hover:text-red-700 text-sm">
                                    <i class="bi bi-trash"></i> <?php echo e(__('Remove')); ?>

                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                        <?php echo e(__('Language')); ?>

                                    </label>
                                    <select :name="'messages[' + index + '][language]'" required
                                            x-model="message.language"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                        <option value="english">English</option>
                                        <option value="arabic">Arabic</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                        <?php echo e(__('Message Type')); ?>

                                    </label>
                                    <select :name="'messages[' + index + '][message_type]'" required
                                            x-model="message.message_type"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                        <option value="text">Text</option>
                                        <option value="buttons">Buttons</option>
                                        <option value="list">List</option>
                                        <option value="template">Template</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                    <?php echo e(__('Message Content')); ?>

                                </label>
                                <textarea :name="'messages[' + index + '][message_content]'" required rows="3"
                                          x-model="message.message_content"
                                          placeholder="Enter your message text here..."
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white"></textarea>
                            </div>

                            <!-- Buttons JSON (if type is buttons) -->
                            <div x-show="message.message_type === 'buttons'" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                    <?php echo e(__('Buttons (JSON)')); ?>

                                </label>
                                <textarea :name="'messages[' + index + '][buttons]'" rows="4"
                                          x-model="message.buttons"
                                          placeholder='[{"type":"reply","reply":{"id":"button_id","title":"Button Title"}}]'
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono text-sm"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Example: [{"type":"reply","reply":{"id":"english","title":"English"}}]</p>
                            </div>

                            <!-- List Sections JSON (if type is list) -->
                            <div x-show="message.message_type === 'list'" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                    <?php echo e(__('List Sections (JSON)')); ?>

                                </label>
                                <textarea :name="'messages[' + index + '][list_sections]'" rows="4"
                                          x-model="message.list_sections"
                                          placeholder='[{"title":"Section Title","rows":[{"id":"row_id","title":"Row Title"}]}]'
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono text-sm"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Example: [{"title":"Options","rows":[{"id":"opt1","title":"Option 1"}]}]</p>
                            </div>

                            <!-- Template Name (if type is template) -->
                            <div x-show="message.message_type === 'template'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                    <?php echo e(__('Template Name')); ?>

                                </label>
                                <input type="text" :name="'messages[' + index + '][template_name]'"
                                       x-model="message.template_name"
                                       placeholder="e.g., welcome_template"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Triggers Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo e(__('Triggers')); ?></h3>
                    <button type="button" @click="addTrigger()" 
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        <i class="bi bi-plus-circle mr-1"></i>
                        <?php echo e(__('Add Trigger')); ?>

                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(trigger, index) in triggers" :key="index">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
                            <div class="flex justify-between items-start mb-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300" 
                                      x-text="'Trigger ' + (index + 1)"></span>
                                <button type="button" @click="removeTrigger(index)"
                                        class="text-red-600 hover:text-red-700 text-sm">
                                    <i class="bi bi-trash"></i> <?php echo e(__('Remove')); ?>

                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                        <?php echo e(__('Trigger Type')); ?>

                                    </label>
                                    <select :name="'triggers[' + index + '][trigger_type]'" required
                                            x-model="trigger.trigger_type"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                        <option value="text">Text Input</option>
                                        <option value="button_reply">Button Reply</option>
                                        <option value="list_reply">List Reply</option>
                                        <option value="state">State Condition</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                        <?php echo e(__('Trigger Value')); ?>

                                    </label>
                                    <input type="text" :name="'triggers[' + index + '][trigger_value]'" required
                                           x-model="trigger.trigger_value"
                                           placeholder="e.g., hi, hello, start"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                    <p class="text-xs text-gray-500 mt-1"><?php echo e(__('Comma-separated for multiple values')); ?></p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                        <?php echo e(__('Next Step')); ?>

                                    </label>
                                    <select :name="'triggers[' + index + '][next_step_id]'"
                                            x-model="trigger.next_step_id"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                        <option value=""><?php echo e(__('None (End Flow)')); ?></option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo e(__('Create other steps first to link them')); ?></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="bi bi-check-circle mr-1"></i>
                    <?php echo e(__('Create Step')); ?>

                </button>
                <a href="<?php echo e(route('admin.flow-builder.index')); ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    <?php echo e(__('Cancel')); ?>

                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function flowStepForm() {
    return {
        messages: [{
            language: 'english',
            message_type: 'text',
            message_content: '',
            buttons: '',
            list_sections: '',
            template_name: ''
        }],
        
        triggers: [],

        addMessage() {
            this.messages.push({
                language: 'english',
                message_type: 'text',
                message_content: '',
                buttons: '',
                list_sections: '',
                template_name: ''
            });
        },

        removeMessage(index) {
            if (this.messages.length > 1) {
                this.messages.splice(index, 1);
            } else {
                alert('You must have at least one message');
            }
        },

        addTrigger() {
            this.triggers.push({
                trigger_type: 'text',
                trigger_value: '',
                next_step_id: ''
            });
        },

        removeTrigger(index) {
            this.triggers.splice(index, 1);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/flow-builder/create.blade.php ENDPATH**/ ?>