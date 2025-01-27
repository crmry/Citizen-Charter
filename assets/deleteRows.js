// Delete functionality for Citizen Charter Generator
function deleteRequirementRow(row) {
    const rowIndex = row.rowIndex - 1; // Subtract 1 to account for header
    
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
    
    // Update the indices of remaining editors
    requirementEditors.forEach(editor => {
        if (editor.index > rowIndex) {
            editor.index--;
        }
    });
}

function deleteProcessOverviewRow(row) {
    const rowIndex = row.rowIndex - 1; // Subtract 1 to account for header
    
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
    
    // Update the indices of remaining editors
    processOverviewEditors.forEach(editor => {
        if (editor.index > rowIndex) {
            editor.index--;
        }
    });
}