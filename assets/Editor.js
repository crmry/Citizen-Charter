let editor;
let requirementEditors = [];
let processOverviewEditors = [];

// Initialize main editor
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

    const editorFields = [
        'clientSteps',
        'agencyActions',
        'feesToBePaid',
        'processingTime',
        'personResponsible'
    ];

    editorFields.forEach(field => {
        ClassicEditor
            .create(document.querySelector(`#${field}-${rowIndex}`), editorConfig)
            .then(editor => {
                processOverviewEditors.push({
                    type: field,
                    index: rowIndex,
                    editor: editor
                });
            })
            .catch(error => {
                console.error(`Error initializing ${field} editor:`, error);
            });
    });
}