<div id="invoice-cheque">
        <!-- Organization Header -->
        <h2 class="bold">
            O‘ZBEKISTON RESPUBLIKASI VAZIRLAR MAHKAMASI HUZURIDAGI<br>
            AGROSANOAT MAJMUI USTIDAN NAZORAT QILISH INSPEKSIYASI<br>
            QOSHIDAGI “QISHLOQ XO‘JALIGI MAHSULOTLARI SIFATINI<br>
            BAHOLASH MARKAZI” DAVLAT MUASSASASI
        </h2>

        <!-- Document Title -->
        <h1>
            Paxta mahsuloti va uni qayta ishlashdan olingan (ikkilamchi) <br>
            mahsulotlarni sinash natijalari bo'yicha <br>
            XULOSA <span class="serif">№</span>{{ $test->laboratory_final_results->number + $i }}
        </h1>

        <!-- Region + Date -->
        <table class="section-table region-date-table">
            <tr>
                <td class="underline">
                    {{ $test->test_program->application->decision->laboratory->city->region->name }}.
                </td>
                <td class="right underline">
                    {{ $formattedDate }} y.
                </td>
            </tr>
        </table>

        <!-- Applicant + Application ID -->
        <table class="section-table">
            <tr>
                <td>
                    <span class="bold">Buyurtma beruvchining nomi:</span>
                    <span class="serif">{{ $test->test_program->application->organization->name }}</span>
                </td>
            </tr>
            <tr>
                 <td>
                    <span class="bold">Buyurtma raqami:</span>
                    {{ $test->final_conclusion_result?->order_number }} {{ date_format(date_create($test->test_program->application->date), 'd.m.Y') }} y.
                </td>
            </tr>
        </table>

        <!-- Invoice + Auto Number (Editable) -->
        <table class="section-table">
            <tr>
                <td style="width:65%;">
                    <span class="bold">Invoys raqami:</span>
                    <span class="editable-wrapper editable-field" data-field="invoice_number" data-test-id="{{ $test->id }}">
                        <span class="editable-content serif">{{ $test->final_conclusion_result?->invoice_number }}</span>
                        <button class="edit-icon no-print" title="Tahrirlash">✏️</button>
                    </span>
                </td>
                <td style="width:35%;" class="right">
                    <span class="bold">Avtotransport raqami:</span>
                    <span class="editable-wrapper editable-field" data-field="vehicle_number" data-test-id="{{ $test->id }}">
                        <span class="editable-content">{{ $test->final_conclusion_result->vehicle_number ?? '-' }}</span>
                        <button class="edit-icon no-print" title="Tahrirlash">✏️</button>
                    </span>
                </td>
            </tr>
        </table>

        <!-- Batch info -->
        <table class="section-table batch-info-table">
            <tr>
                <td style="width:50%;">
                    <span class="bold">To'da raqami:</span> {{ $test->test_program->application->crops->party_number }}
                </td>
                <td style="width:50%;" class="right">
                    <span class="bold">Toy soni:</span>  {{ $test->test_program->application->crops->toy_count }} ta
                </td>
            </tr>
        </table>

        <!-- Amount -->
        <table class="section-table">
            <tr>
                <td style="width:50%;" class="left">
                    <span class="bold">To'da og'irligi(netto):</span> {{ number_format($test->test_program->application->crops->amount, 0, '.', ' ') }} kg
                </td>
                @if($test->final_conclusion_result?->cmr_number)
                    <td class="right">
                        <span class="bold">CMR №:</span>
                        {{ $test->final_conclusion_result?->cmr_number }}
                    </td>
                @endif
            </tr>
        </table>

        <!-- Editable Conclusion Text Parts -->
        <table class="section-table conclusion-table">
            @if(!empty($test->final_conclusion_result->conclusion_part_1))
            <tr class="editable-row" data-part="conclusion_part_1" data-test-id="{{ $test->id }}">
                <td>
                    <div class="editable-wrapper">
                        <span> {{ $formattedDate2 }}</span>
                        <span class="editable-content">{{ $test->final_conclusion_result->conclusion_part_1 }}</span>
                        <button class="edit-icon no-print" title="Tahrirlash">✏️</button>
                    </div>
                </td>
            </tr>
            @endif
            
            @if(!empty($test->final_conclusion_result->conclusion_part_2))
            <tr class="editable-row" data-part="conclusion_part_2" data-test-id="{{ $test->id }}">
                <td>
                    <div class="editable-wrapper">
                        <span class="editable-content">{{ $test->final_conclusion_result->conclusion_part_2 }}</span>
                        <button class="edit-icon no-print" title="Tahrirlash">✏️</button>
                    </div>
                </td>
            </tr>
            @endif
            
            @if(!empty($test->final_conclusion_result->conclusion_part_3))
            <tr class="editable-row" data-part="conclusion_part_3" data-test-id="{{ $test->id }}">
                <td>
                    <div class="editable-wrapper">
                        <span class="editable-content">{{ $test->final_conclusion_result->conclusion_part_3 }}</span>
                        <button class="edit-icon no-print" title="Tahrirlash">✏️</button>
                    </div>
                </td>
            </tr>
            @endif
        </table>

        <!-- Final Note -->
        <p><strong>Sinov natijalari, sinovdan o'tkazilgan namunalarga tegishlidir.</strong></p>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="section-table">
                <tr>
                    <td style="width: 40%;" class="bold">
                        {{ $test->test_program->application->decision->laboratory->name }} boshlig'i
                    </td>
                    <td style="width: 30%;" class="center">
                        <div class="qr-container">
                            @if(!isset($t) && !empty($qrCode))
                                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                            @endif
                            @if(isset($sert_number))
                                <span>{{ substr($sert_number, 2) }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="width: 30%;" class="right bold">
                        <div class="director-name">
                            {{ $test->laboratory_final_results->director->lastname }}
                            {{ substr($test->laboratory_final_results->director->name, 0, 1) }}.
                        </div>
                    </td>
                </tr>
            </table>

            <table class="section-table">
                <tr class="specialist-row">
                    <td style="width: 50%;" class="bold">
                        Laboratoriya mutaxassisi
                    </td>
                    <td style="width: 50%;" class="center serif">
                        {{ optional($test->laboratory_final_results->operator)->name ?? '_________________' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
            
            // Handle inline field editing (invoice_number, auto_number)
            const inlineFields = document.querySelectorAll('.editable-field');
            
            inlineFields.forEach(wrapper => {
                const editIcon = wrapper.querySelector('.edit-icon');
                const contentSpan = wrapper.querySelector('.editable-content');
                const fieldName = wrapper.dataset.field;
                const testId = wrapper.dataset.testId;
                
                let originalContent = contentSpan.textContent;
                let isEditing = false;
                
                editIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (isEditing) return;
                    
                    isEditing = true;
                    originalContent = contentSpan.textContent;
                    
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = originalContent;
                    input.className = 'inline-edit-input';
                    
                    const saveBtn = document.createElement('button');
                    saveBtn.innerHTML = '✓ Saqlash';
                    saveBtn.className = 'btn btn-save';
                    saveBtn.style.padding = '6px 12px';
                    saveBtn.style.fontSize = '12px';
                    saveBtn.type = 'button';
                    
                    const cancelBtn = document.createElement('button');
                    cancelBtn.innerHTML = '✕ Bekor';
                    cancelBtn.className = 'btn btn-cancel';
                    cancelBtn.style.padding = '6px 12px';
                    cancelBtn.style.fontSize = '12px';
                    cancelBtn.type = 'button';
                    
                    const editWrapper = document.createElement('span');
                    editWrapper.className = 'inline-edit-wrapper';
                    editWrapper.appendChild(input);
                    editWrapper.appendChild(saveBtn);
                    editWrapper.appendChild(cancelBtn);
                    
                    wrapper.innerHTML = '';
                    wrapper.appendChild(editWrapper);
                    
                    input.focus();
                    input.select();
                    
                    const restore = () => {
                        wrapper.innerHTML = '';
                        contentSpan.textContent = originalContent;
                        wrapper.appendChild(contentSpan);
                        wrapper.appendChild(editIcon);
                        isEditing = false;
                    };
                    
                    cancelBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        restore();
                    });
                    
                    saveBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const newValue = input.value.trim();
                        
                        if (newValue === originalContent) {
                            restore();
                            return;
                        }
                        
                        saveBtn.disabled = true;
                        cancelBtn.disabled = true;
                        saveBtn.innerHTML = '<span class="loading"></span> Saqlanmoqda...';
                        
                        saveField(testId, fieldName, newValue)
                            .then(response => {
                                if (response.success) {
                                    originalContent = newValue;
                                    contentSpan.textContent = newValue;
                                    showMessage('✓ Muvaffaqiyatli saqlandi!', 'success');
                                    restore();
                                } else {
                                    showMessage('✕ Xatolik: ' + (response.message || 'Noma\'lum xatolik!'), 'error');
                                    saveBtn.disabled = false;
                                    cancelBtn.disabled = false;
                                    saveBtn.innerHTML = '✓ Saqlash';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showMessage('✕ Serverda xatolik yuz berdi!', 'error');
                                saveBtn.disabled = false;
                                cancelBtn.disabled = false;
                                saveBtn.innerHTML = '✓ Saqlash';
                            });
                    });
                    
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            saveBtn.click();
                        } else if (e.key === 'Escape') {
                            cancelBtn.click();
                        }
                    });
                });
            });
            
            // Handle conclusion paragraph editing
            const editableRows = document.querySelectorAll('.editable-row');
            
            editableRows.forEach(row => {
                const wrapper = row.querySelector('.editable-wrapper');
                const editIcon = wrapper.querySelector('.edit-icon');
                const contentSpan = wrapper.querySelector('.editable-content');
                const td = row.querySelector('td');
                const part = row.dataset.part;
                const testId = row.dataset.testId;
                
                let originalContent = contentSpan.textContent;
                let isEditing = false;
                
                editIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (isEditing) return;
                    
                    isEditing = true;
                    originalContent = contentSpan.textContent;
                    
                    const textarea = document.createElement('textarea');
                    textarea.value = originalContent;
                    textarea.className = 'edit-mode-textarea';
                    
                    const actionsDiv = document.createElement('div');
                    actionsDiv.className = 'edit-actions';
                    
                    const saveBtn = document.createElement('button');
                    saveBtn.textContent = '✓ Saqlash';
                    saveBtn.className = 'btn btn-save';
                    saveBtn.type = 'button';
                    
                    const cancelBtn = document.createElement('button');
                    cancelBtn.textContent = '✕ Bekor qilish';
                    cancelBtn.className = 'btn btn-cancel';
                    cancelBtn.type = 'button';
                    
                    actionsDiv.appendChild(cancelBtn);
                    actionsDiv.appendChild(saveBtn);
                    
                    td.innerHTML = '';
                    td.appendChild(textarea);
                    td.appendChild(actionsDiv);
                    td.classList.add('edit-mode');
                    
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                    
                    const restoreContent = () => {
                        td.classList.remove('edit-mode');
                        td.innerHTML = '';
                        wrapper.innerHTML = '';
                        contentSpan.textContent = originalContent;
                        wrapper.appendChild(contentSpan);
                        wrapper.appendChild(editIcon);
                        td.appendChild(wrapper);
                        isEditing = false;
                    };
                    
                    cancelBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        restoreContent();
                    });
                    
                    saveBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const newContent = textarea.value.trim();
                        
                        if (!newContent) {
                            showMessage('✕ Matn bo\'sh bo\'lishi mumkin emas!', 'error');
                            return;
                        }
                        
                        if (newContent === originalContent) {
                            restoreContent();
                            return;
                        }
                        
                        saveBtn.disabled = true;
                        cancelBtn.disabled = true;
                        saveBtn.innerHTML = '<span class="loading"></span> Saqlanmoqda...';
                        
                        saveConclusion(testId, part, newContent)
                            .then(response => {
                                if (response.success) {
                                    originalContent = newContent;
                                    contentSpan.textContent = newContent;
                                    showMessage('✓ Muvaffaqiyatli saqlandi!', 'success');
                                    restoreContent();
                                } else {
                                    showMessage('✕ Xatolik: ' + (response.message || 'Noma\'lum xatolik!'), 'error');
                                    saveBtn.disabled = false;
                                    cancelBtn.disabled = false;
                                    saveBtn.textContent = '✓ Saqlash';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showMessage('✕ Serverda xatolik yuz berdi!', 'error');
                                saveBtn.disabled = false;
                                cancelBtn.disabled = false;
                                saveBtn.textContent = '✓ Saqlash';
                            });
                    });
                    
                    textarea.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            cancelBtn.click();
                        }
                    });
                });
            });
            
            // AJAX function to save inline fields
            function saveField(testId, field, value) {
                return fetch('/api/tests/' + testId + '/field', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        field: field,
                        value: value
                    })
                })
                .then(response => response.json())
                .catch(error => {
                    console.error('Fetch error:', error);
                    return { success: false, message: 'Network error' };
                });
            }
            
            // AJAX function to save conclusion
            function saveConclusion(testId, part, content) {
                return fetch('/api/tests/' + testId + '/conclusion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        field: part,
                        value: content
                    })
                })
                .then(response => response.json())
                .catch(error => {
                    console.error('Fetch error:', error);
                    return { success: false, message: 'Network error' };
                });
            }
            
            // Show message function - FIXED FOR PROPER DISPLAY
            function showMessage(text, type) {
                // Remove existing message if any
                const existingMessages = document.querySelectorAll('.message');
                existingMessages.forEach(msg => msg.remove());
                
                // Create message container if it doesn't exist
                let messageContainer = document.getElementById('message-container');
                if (!messageContainer) {
                    messageContainer = document.createElement('div');
                    messageContainer.id = 'message-container';
                    document.body.appendChild(messageContainer);
                }
                
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message ' + type;
                messageDiv.innerHTML = text;
                messageDiv.setAttribute('role', 'alert');
                messageDiv.setAttribute('aria-live', 'polite');
                
                messageContainer.appendChild(messageDiv);
                
                // Force reflow to ensure animation triggers
                messageDiv.offsetHeight;
                
                // Auto-remove after 4 seconds
                const timeoutId = setTimeout(() => {
                    messageDiv.style.animation = 'slideOut 0.3s ease-in forwards';
                    setTimeout(() => {
                        if (messageDiv.parentNode) {
                            messageDiv.remove();
                        }
                    }, 300);
                }, 4000);
                
                // Allow manual removal on click
                messageDiv.addEventListener('click', () => {
                    clearTimeout(timeoutId);
                    messageDiv.style.animation = 'slideOut 0.3s ease-in forwards';
                    setTimeout(() => {
                        if (messageDiv.parentNode) {
                            messageDiv.remove();
                        }
                    }, 300);
                });
            }
        });
    </script>

    <style>
        /* Message container */
        #message-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .message {
            pointer-events: all;
            padding: 14px 24px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 420px;
            backdrop-filter: blur(4px);
        }

        .message.success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-left: 4px solid rgba(255, 255, 255, 0.4);
        }

        .message.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-left: 4px solid rgba(255, 255, 255, 0.4);
        }

        @keyframes slideIn {
            from {
                transform: translateX(420px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(420px);
                opacity: 0;
            }
        }

        .edit-mode-textarea {
            width: 100%;
            min-height: 100px;
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.8;
            text-align: justify;
            padding: 12px;
            border: 2px solid #667eea;
            border-radius: 6px;
            resize: vertical;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .edit-mode-textarea:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
    </style>