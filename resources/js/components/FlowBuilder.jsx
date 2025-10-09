import React, { useState } from 'react';
import { GripVertical, Plus, Edit2, Trash2, ChevronDown, ChevronUp } from 'lucide-react';

function FlowBuilderDashboard({ initialSteps = [] }) {
  const [steps, setSteps] = useState(initialSteps);
  const [expandedSteps, setExpandedSteps] = useState(new Set());
  const [draggedIndex, setDraggedIndex] = useState(null);
  const [dragOverIndex, setDragOverIndex] = useState(null);

  const handleDragStart = (index) => {
    setDraggedIndex(index);
  };

  const handleDragEnter = (index) => {
    if (draggedIndex === null) return;
    setDragOverIndex(index);
  };

  const handleDragEnd = () => {
    if (draggedIndex === null || dragOverIndex === null || draggedIndex === dragOverIndex) {
      setDraggedIndex(null);
      setDragOverIndex(null);
      return;
    }

    const newSteps = [...steps];
    const [removed] = newSteps.splice(draggedIndex, 1);
    newSteps.splice(dragOverIndex, 0, removed);
    
    setSteps(newSteps);
    updateOrderOnServer(newSteps.map((step, idx) => ({ id: step.id, order: idx })));
    
    setDraggedIndex(null);
    setDragOverIndex(null);
  };

  const updateOrderOnServer = async (orderedSteps) => {
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      await fetch('/varsity/public/index.php/admin/flow-builder/reorder', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
        },
        body: JSON.stringify({ steps: orderedSteps }),
      });
    } catch (error) {
      console.error('Failed to update order:', error);
    }
  };

  const toggleExpand = (stepId) => {
    const newExpanded = new Set(expandedSteps);
    if (newExpanded.has(stepId)) {
      newExpanded.delete(stepId);
    } else {
      newExpanded.add(stepId);
    }
    setExpandedSteps(newExpanded);
  };

  return (
    <div className="max-w-6xl mx-auto p-6 space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Flow Builder</h1>
          <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Configure your WhatsApp chat flow by dragging steps into the desired order
          </p>
        </div>
        <a
          href="/varsity/public/index.php/admin/flow-builder/create"
          className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          <Plus size={20} />
          Add Step
        </a>
      </div>

      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        {steps.length === 0 ? (
          <div className="p-12 text-center">
            <div className="text-gray-400 mb-4">
              <svg className="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No flow steps yet</h3>
            <p className="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first flow step</p>
            <a
              href="/varsity/public/index.php/admin/flow-builder/create"
              className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <Plus size={20} />
              Create First Step
            </a>
          </div>
        ) : (
          <div className="divide-y divide-gray-200 dark:divide-gray-700">
            {steps.map((step, index) => (
              <FlowStep
                key={step.id}
                step={step}
                index={index}
                isExpanded={expandedSteps.has(step.id)}
                onToggleExpand={() => toggleExpand(step.id)}
                isDragging={draggedIndex === index}
                isDragOver={dragOverIndex === index}
                onDragStart={() => handleDragStart(index)}
                onDragEnter={() => handleDragEnter(index)}
                onDragEnd={handleDragEnd}
              />
            ))}
          </div>
        )}
      </div>

      <div className="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
        <h3 className="font-medium text-blue-900 dark:text-blue-100 mb-2">Tips:</h3>
        <ul className="text-sm text-blue-800 dark:text-blue-200 space-y-1">
          <li>• Drag steps to reorder your chat flow</li>
          <li>• The first step will be executed when a user starts a conversation</li>
          <li>• Configure triggers to determine which step executes next</li>
          <li>• Inactive steps will be skipped in the flow</li>
        </ul>
      </div>
    </div>
  );
}

function FlowStep({ 
  step, 
  index, 
  isExpanded, 
  onToggleExpand,
  isDragging,
  isDragOver,
  onDragStart,
  onDragEnter,
  onDragEnd
}) {
  const getStepTypeColor = (type) => {
    const colors = {
      welcome: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
      language_selection: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
      menu: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
      catalog: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
      support: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
      collect_name: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
      collect_address: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
      payment_confirmation: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
      order_processing: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
      custom: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    };
    return colors[type] || colors.custom;
  };

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this step?')) return;
    
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      const response = await fetch(`/varsity/public/index.php/admin/flow-builder/${step.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
        },
      });
      
      if (response.ok) {
        window.location.reload();
      }
    } catch (error) {
      console.error('Failed to delete step:', error);
    }
  };

  return (
    <div 
      draggable
      onDragStart={(e) => {
        e.dataTransfer.effectAllowed = 'move';
        onDragStart();
      }}
      onDragEnter={onDragEnter}
      onDragOver={(e) => e.preventDefault()}
      onDragEnd={onDragEnd}
      className={`bg-white dark:bg-gray-800 transition-all ${
        isDragging ? 'opacity-50' : 'opacity-100'
      } ${isDragOver ? 'border-t-4 border-blue-500' : ''}`}
    >
      <div className="p-4">
        <div className="flex items-center gap-4">
          <div className="cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <GripVertical size={20} />
          </div>

          <div className="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">
            {index + 1}
          </div>

          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-2 mb-1">
              <h3 className="text-base font-semibold text-gray-900 dark:text-white truncate">
                {step.name}
              </h3>
              <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStepTypeColor(step.step_type)}`}>
                {step.step_type.replace(/_/g, ' ')}
              </span>
              {!step.is_active && (
                <span className="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                  Inactive
                </span>
              )}
            </div>
            <div className="text-sm text-gray-600 dark:text-gray-400">
              {step.messages?.length || 0} message(s) • {step.triggers?.length || 0} trigger(s)
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={onToggleExpand}
              className="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            >
              {isExpanded ? <ChevronUp size={20} /> : <ChevronDown size={20} />}
            </button>
            
            <a
              href={`/varsity/public/index.php/admin/flow-builder/${step.id}/edit`}
              className="p-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
            >
              <Edit2 size={20} />
            </a>
            
            <button
              onClick={handleDelete}
              className="p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
            >
              <Trash2 size={20} />
            </button>
          </div>
        </div>

        {isExpanded && (
          <div className="mt-4 pl-12 space-y-4">
            {step.messages && step.messages.length > 0 && (
              <div>
                <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Messages:</h4>
                <div className="space-y-2">
                  {step.messages.map((msg, idx) => (
                    <div key={idx} className="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="text-xs font-medium text-gray-600 dark:text-gray-400">
                          {msg.language}
                        </span>
                        <span className="text-xs text-gray-500">•</span>
                        <span className="text-xs text-gray-600 dark:text-gray-400">
                          {msg.message_type}
                        </span>
                      </div>
                      <p className="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                        {msg.message_content}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {step.triggers && step.triggers.length > 0 && (
              <div>
                <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Triggers:</h4>
                <div className="space-y-2">
                  {step.triggers.map((trigger, idx) => (
                    <div key={idx} className="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                      <div className="flex items-center gap-2 text-xs">
                        <span className="font-medium text-gray-600 dark:text-gray-400">
                          {trigger.trigger_type}:
                        </span>
                        <span className="text-gray-700 dark:text-gray-300">
                          {trigger.trigger_value}
                        </span>
                        {trigger.next_step_id && (
                          <>
                            <span className="text-gray-500">→</span>
                            <span className="text-blue-600 dark:text-blue-400">
                              Step #{trigger.next_step_id}
                            </span>
                          </>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}

export default FlowBuilderDashboard;