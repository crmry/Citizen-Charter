<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Charter Generator</title>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 0.5em;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            line-height: 1.5;
        }
        .checkbox-group {
            margin-top: 5px;
        }
        .checkbox-group div {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 5px;
            transform: scale(1);
            cursor: pointer;
        }
        .requirements-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .requirements-table th, .requirements-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left; 
        }
        .requirements-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .button-container {
            display: flex;
            justify-content: right;
            align-items: center;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 1rem;
        }
        button:hover {
            background-color: #45a049;
        }
        .ck-editor__editable {
            min-height: 100px !important;
            font-size: 1rem
        }
        table 
        {
          table-layout:fixed;
          width:100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Citizen Charter Generator</h2>
        
        <div class="form-group">
            <label for="serviceNameInput">Service Name:</label>
            <input type="text" id="serviceNameInput">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <div id="editor"></div>
        </div>

        <table class="requirements-table">
            <tr>
                <th>Office or Division</th>
                <td>
                    <textarea name="office" rows="2"></textarea>
                </td>
            </tr>
            <tr>
                <th>Classification</th>
                <td>
                    <select name="classification">
                        <option value="Simple">Simple</option>
                        <option value="Complex">Complex</option>
                        <option value="Highly Technical">Highly Technical</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Type of Transaction</th>
                <td>
                    <div class="checkbox-group">
                        <div>
                            <input type="checkbox" name="transaction_type" value="Government to Citizen (G2C)">
                            <label>Government to Citizen (G2C)</label>
                        </div>
                        <div>
                            <input type="checkbox" name="transaction_type" value="Government to Business (G2B)">
                            <label>Government to Business (G2B)</label>
                        </div>
                        <div>
                            <input type="checkbox" name="transaction_type" value="Government to Government (G2G)">
                            <label> Government to Government (G2G)</label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Who May Avail</th>
                <td>
                    <textarea name="who_may_avail" rows="4"></textarea>
                </td>
            </tr>
        </table>

        <div class="requirements-section">
            <h3>Requirements</h3>
            <button type="button" onclick="addRequirementRow()">Add New Requirement Row</button>
            <table class="requirementsChecklistTable" id="requirements-table">
                <thead>
                    <tr>
                        <th>CHECKLIST OF REQUIREMENTS</th>
                        <th>WHERE TO SECURE</th>
                    </tr>
                </thead>
                <tbody id="requirementsTableBody">
                </tbody>
            </table>
        </div>

        <div class="requirements-section">
            <h3>Process Overview</h3>
            <table id="processOverviewTable" class="requirements-table" >
                <thead>
                    <tr>
                        <th>Client Steps</th>
                        <th>Agency Actions</th>
                        <th>Fees to be Paid</th>
                        <th>Processing Time</th>
                        <th>Person Responsible</th>
                    </tr>
                </thead>
                <tbody id="processOverviewTableBody">
                </tbody>
                <tfoot>
                        <th></th>
                        <th></th>
                        <th><input type="text" id='FeesToBePaid'></th>
                        <th><input type="text" id='ProcessingTime'></th>
                        <th></th>
                </tfoot>
            </table>
        </div>
        <button onclick="addProcessOverviewRow()">Add New Process Overview Row</button>             
        <div class="button-container">
        <button  onclick="openPreview()">Preview</button>
    </div>
    </div>

    <script>
        let editor;
        let requirementEditors = [];
        let processOverviewEditors = [];

        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(newEditor => {
                editor = newEditor;
                console.log('CKEditor initialized');
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });

        function addRequirementRow() {
            const tableBody = document.getElementById('requirementsTableBody');
            const newRow = document.createElement('tr');
            const rowIndex = tableBody.children.length;
            
            newRow.innerHTML = `
                <td><div id="requirement-${rowIndex}"></div></td>
                <td><div id="whereToSecure-${rowIndex}"></div></td>
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

        function addProcessOverviewRow() {
            const tableBody = document.getElementById('processOverviewTableBody');
            const newRow = document.createElement('tr');
            const rowIndex = tableBody.children.length;

            newRow.innerHTML = `
                <td><div id="clientSteps-${rowIndex}"></div></td>
                <td><div id="agencyActions-${rowIndex}"></div></td>
                <td><div id="feesToBePaid-${rowIndex}"></div></td>
                <td><div id="processingTime-${rowIndex}"></div></td>
                <td><div id="personResponsible-${rowIndex}"></div></td>
            `;
            tableBody.appendChild(newRow);

            const editorConfig = {
                toolbar: ['bold', 'italic', 'bulletedList', 'numberedList'],
            };

            ClassicEditor
                .create(document.querySelector(`#clientSteps-${rowIndex}`), editorConfig)
                .then(editor => {
                    processOverviewEditors.push({
                        type: 'clientSteps',
                        index: rowIndex,
                        editor: editor
                    });
                })
                .catch(error => {
                    console.error('Error initializing clientSteps editor:', error);
                });

            ClassicEditor
                .create(document.querySelector(`#agencyActions-${rowIndex}`), editorConfig)
                .then(editor => {
                    processOverviewEditors.push({
                        type: 'agencyActions',
                        index: rowIndex,
                        editor: editor
                    });
                })
                .catch(error => {
                    console.error('Error initializing agencyActions editor:', error);
                });

            ClassicEditor
                .create (document.querySelector(`#feesToBePaid-${rowIndex}`), editorConfig)
                .then(editor => {
                    processOverviewEditors.push({
                        type: 'feesToBePaid',
                        index: rowIndex,
                        editor: editor
                    });
                })
                .catch(error => {
                    console.error('Error initializing feesToBePaid editor:', error);
                });

            ClassicEditor
                .create(document.querySelector(`#processingTime-${rowIndex}`), editorConfig)
                .then(editor => {
                    processOverviewEditors.push({
                        type: 'processingTime',
                        index: rowIndex,
                        editor: editor
                    });
                })
                .catch(error => {
                    console.error('Error initializing processingTime editor:', error);
                });

            ClassicEditor
                .create(document.querySelector(`#personResponsible-${rowIndex}`), editorConfig)
                .then(editor => {
                    processOverviewEditors.push({
                        type: 'personResponsible',
                        index: rowIndex,
                        editor: editor
                    });
                })
                .catch(error => {
                    console.error('Error initializing personResponsible editor:', error);
                });
        }

        function openPreview() {
            var FeesToBePaid = document.getElementById('FeesToBePaid').value;
            var ProcessingTime = document.getElementById('ProcessingTime').value;
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
                                <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:30%; word-wrap: break-word;">CLIENT STEPS</th>
                                <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:30%; word-wrap: break-word;">AGENCY ACTIONS</th>
                                <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:10%; word-wrap: break-word;">FEES TO BE PAID</th>
                                <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:15%; word-wrap: break-word;">PROCESSING TIME</th>
                                <th style="background-color:#8eaadb; text-align:center; page-break-before:avoid; width:15%; word-wrap: break-word;">PERSON RESPONSIBLE</th>
                            ${processOverview.map(proc => `
                                <tr>
                                    <td>${proc.clientSteps}</td>
                                    <td>${proc.agencyActions}</td>
                                    <td>${proc.feesToBePaid}</td>
                                    <td>${proc.processingTime}</td>
                                    <td>${proc.personResponsible}</td>
                                </tr>
                            `).join('')}
                                <th style="background-color:#8eaadb; text-align:center; width:30%;"></th>
                                <th style="background-color:#8eaadb; text-align:right; width:30%;">TOTAL</th>
                                <th style="background-color:#8eaadb; text-align:center; width:10%;">${FeesToBePaid}</th>
                                <th style="background-color:#8eaadb; text-align:center; width:15%;">${ProcessingTime}</th>
                                <th style="background-color:#8eaadb; text-align:center; width:15%;"></th>
                        </tbody>
                    </table>
                </body>
                </html>
            `);
            previewWindow.document.close();
        }
    </script>
</body>
</html>
