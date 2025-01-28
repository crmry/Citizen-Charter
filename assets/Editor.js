let editor;
let requirementEditors = [];
let processOverviewEditors = [];
let editorCounter = 0; // Initialize a counter for editor IDs

ClassicEditor
    .create(document.querySelector('#editor'))
    .then(newEditor => {
        editor = newEditor;
        console.log('CKEditor initialized');
    })
    .catch(error => {
        console.error('CKEditor initialization error:', error);
    });

// Add requirement row
function addRequirementRow() {
    const tableBody = document.getElementById('requirementsTableBody');
    const newRow = document.createElement('tr');
    
    // Use the counter to generate unique ID
    const rowIndex = editorCounter++;

    newRow.innerHTML = `
        <td><div id="requirement-${rowIndex}"></div></td>
        <td><div id="whereToSecure-${rowIndex}"></div></td>
        <td>
            <button class="delete-btn" onclick="deleteRequirementRow(this.parentNode.parentNode)">Delete</button>
        </td>
    `;
    tableBody.appendChild(newRow);

    const editorConfig = {
        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList'],
    };

    ClassicEditor
        .create(document.querySelector(`#requirement-${rowIndex}`), editorConfig)
        .then(editor => {
            requirementEditors.push({
                type: 'requirement',
                index: rowIndex,
                editor: editor
            });
        })
        .catch(error => {
            console.error('Error initializing requirement editor:', error);
        });

    ClassicEditor
        .create(document.querySelector(`#whereToSecure-${rowIndex}`), editorConfig)
        .then(editor => {
            requirementEditors.push({
                type: 'whereToSecure',
                index: rowIndex,
                editor: editor
            });
        })
        .catch(error => {
            console.error('Error initializing whereToSecure editor:', error);
        });
}

// Add process overview row
function addProcessOverviewRow() {
    const tableBody = document.getElementById('processOverviewTableBody');
    const newRow = document.createElement('tr');

    // Use the counter to generate unique ID
    const rowIndex = editorCounter++;

    newRow.innerHTML = `
        <td><div id="clientSteps-${rowIndex}"></div></td>
        <td><div id="agencyActions-${rowIndex}"></div></td>
        <td><div id="feesToBePaid-${rowIndex}"></div></td>
        <td><div id="processingTime-${rowIndex}"></div></td>
        <td><div id="personResponsible-${rowIndex}"></div></td>
        <td><button class="delete-btn" onclick="deleteProcessOverviewRow(this.parentNode.parentNode)">Delete</button></td>
    `;
    tableBody.appendChild(newRow);

    const editorConfig = {
        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList'],
    };

    const fieldTypes = [
        'clientSteps',
        'agencyActions',
        'feesToBePaid',
        'processingTime',
        'personResponsible',
    ];

    fieldTypes.forEach(type => {
        ClassicEditor.create(document.querySelector(`#${type}-${rowIndex}`), editorConfig)
            .then(editor => {
                processOverviewEditors.push({
                    type: type,
                    index: rowIndex,
                    editor: editor,
                });
            })
            .catch(error => {
                console.error(`Error initializing ${type} editor:`, error);
            });
    });
}

// Delete requirement row
function deleteRequirementRow(row) {
    const rowIndex = row.rowIndex - 1;

    // Remove the editors associated with this row
    requirementEditors = requirementEditors.filter(editor => {
        if (editor.index === rowIndex) {
            editor.editor.destroy();
            return false;
        }
        return true;
    });

    // Remove the row from the table
    row.remove();

        // Rebuild the preview after deletion
        openPreview(); // This triggers a fresh preview rebuild

}

// Delete process overview row
function deleteProcessOverviewRow(row) {
    const rowIndex = row.rowIndex - 1;

    // Remove the editors associated with this row
    processOverviewEditors = processOverviewEditors.filter(editor => {
        if (editor.index === rowIndex) {
            editor.editor.destroy();
            return false;
        }
        return true;
    });

    // Remove the row from the table
    row.remove();

        // Rebuild the preview after deletion
        openPreview(); // This triggers a fresh preview rebuild

}

function openPreview() {
    var FeesToBePaid = document.getElementById('FeesToBePaid').value || "N/A";
    var ProcessingTime = document.getElementById('ProcessingTime').value || "N/A";
    console.log('Preview button clicked');
    const serviceName = document.getElementById('serviceNameInput').value;
    const description = editor.getData();
    const office = document.querySelector('textarea[name="office"]').value;
    const classification = document.querySelector('select[name="classification"]').value;
    const transactionTypes = Array.from(document.querySelectorAll('input[name="transaction_type"]:checked')).map(el => el.value);
    const whoMayAvail = document.querySelector('textarea[name="who_may_avail"]').value.replace(/\n/g, '<br>');

    let requirements = [];
    requirementEditors.forEach(req => {
        if (req.type === 'requirement') {
            const requirementData = req.editor.getData();
            const whereToSecureEditor = requirementEditors.find(r => r.index === req.index && r.type === 'whereToSecure');
            const whereToSecureData = whereToSecureEditor ? whereToSecureEditor.editor.getData() : '';
            requirements.push({ requirement: requirementData, whereToSecure: whereToSecureData });
        }
    });

    let processOverview = [];
    processOverviewEditors.forEach(proc => {
        if (proc.type === 'clientSteps') {
            const clientStepsData = proc.editor.getData();
            const agencyActionsEditor = processOverviewEditors.find(p => p.index === proc.index && p.type === 'agencyActions');
            const agencyActionsData = agencyActionsEditor ? agencyActionsEditor.editor.getData() : '';
            const feesToBePaidEditor = processOverviewEditors.find(p => p.index === proc.index && p.type === 'feesToBePaid');
            const feesToBePaidData = feesToBePaidEditor ? feesToBePaidEditor.editor.getData() : '';
            const processingTimeEditor = processOverviewEditors.find(p => p.index === proc.index && p.type === 'processingTime');
            const processingTimeData = processingTimeEditor ? processingTimeEditor.editor.getData() : '';
            const personResponsibleEditor = processOverviewEditors.find(p => p.index === proc.index && p.type === 'personResponsible');
            const personResponsibleData = personResponsibleEditor ? personResponsibleEditor.editor.getData() : '';
            processOverview.push({ clientSteps: clientStepsData, agencyActions: agencyActionsData, feesToBePaid: feesToBePaidData, processingTime: processingTimeData, personResponsible: personResponsibleData });
        }
    });

    let previewWindow = window.open('', 'Preview', 'width=816px,height=1056px');
    previewWindow.document.write(`
        <html>
        <head>
            <title>Preview</title>
            <style>
                @media print {
                    table {
                        page-break-inside: auto;
                        border-collapse: collapse;
                    }
                    thead {
                        display: table-header-group;
                    }
                    tfoot {
                        display: table-footer-group;
                    }
                    tr {
                        page-break-inside: avoid;
                        page-break-after: auto;
                    }
                }
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                    vertical-align: top;
                }
                table tr td:first-child {
                    width: 30%;
                    word-break: break-all;
                }
                table tr td:last-child {
                    width: 100%;
                }
                .main-header {
                    text-align: left;
                    margin-bottom: 20px;
                }
                .service-name {
                    font-family: Arial, sans-serif;
                    font-size: 19.2px !important;
                    font-weight: bold !important;
                    margin-bottom: 10px;
                    display: block;
                    width: 100%;
                }
                .service-description {
                    margin-bottom: 10px;
                }
                .requirements-header {
                    font-weight: bold;
                    background-color: #f2f2f2;
                }
                .requirements-table {
                    width: 100%;
                    margin-top: -21px;
                }
                .process-overview-table {
                    margin-top: -21px;
                }
            </style>
        </head>
        <body>
            <div class="main-header">
                <span class="service-name">${serviceName}</span>
                <div class="service-description">${description}</div>
            </div>
            <table class="info-table">
                <tr>
                    <td style="background-color:#8eaadb">Office or Division</td>
                    <td>${office}</td>
                </tr>
                <tr>
                    <td style="background-color:#8eaadb">Classification:</td>
                    <td>${classification}</td>
                </tr>
                <tr>
                    <td style="background-color:#8eaadb">Type of Transaction:</td>
                    <td>${transactionTypes.join(', ')}</td>
                </tr>
                <tr>
                    <td style="background-color:#8eaadb">Who may avail:</td>
                    <td>${whoMayAvail}</td>
                </tr>
            </table>
            <table class="process-overview-table">
                <tbody style="page-break-before:avoid">
                    <tr>
                        <th style="background-color:#8eaadb; text-align:center;" colspan="2">CHECKLIST OF REQUIREMENTS</th>
                        <th style="background-color:#8eaadb; text-align:center;" colspan="3">WHERE TO SECURE</th>
                    </tr>
                    ${requirements.map(req => `
                        <tr>
                            <td colspan="2">${req.requirement}</td>
                            <td colspan="3">${req.whereToSecure}</td>
                        </tr>
                    `).join('')}
                    <tr>
                        <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:30%; word-wrap: break-word;">CLIENT STEPS</th>
                        <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:30%; word-wrap: break-word;">AGENCY ACTIONS</th>
                        <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:10%; word-wrap: break-word;">FEES TO BE PAID</th>
                        <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:15%; word-wrap: break-word;">PROCESSING TIME</th>
                        <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:15%; word-wrap: break-word;">PERSON RESPONSIBLE</th>
                    </tr>
                    ${processOverview.map(proc => `
                        <tr>
                            <td>${proc.clientSteps}</td>
                            <td>${proc.agencyActions}</td>
                            <td>${proc.feesToBePaid}</td>
                            <td>${proc.processingTime}</td>
                            <td>${proc.personResponsible}</td>
                        </tr>
                    `).join('')}
                    <tr>
                        <td style="background-color:#8eaadb; text-align:center; width:30%;"></td>
                        <td style="background-color:#8eaadb; text-align:right; width:30%;">TOTAL</td>
                        <td style="background-color:#8eaadb; text-align:center; width:10%;">${FeesToBePaid}</td>
                        <td style="background-color:#8eaadb; text-align:center; width:15%;">${ProcessingTime}</td>
                        <td style="background-color:#8eaadb; text-align:center; width:15%;"></td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>
    `);
    previewWindow.document.close();
}
