const fs = require('fs');
const path = require('path');

const dir = 'd:/Documentos/Trabajo_Uni/ieeEdu/resources';

const replacements = [
    { regex: /\[#FAFAF5\]/gi, replacement: 'background' },
    { regex: /\[#1A1C19\]/gi, replacement: 'on-background' },
    { regex: /\[#5F5E5E\]/gi, replacement: 'on-surface-variant' },
];

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(file));
        } else {
            if (file.endsWith('.vue') || file.endsWith('.blade.php') || file.endsWith('.css')) {
                results.push(file);
            }
        }
    });
    return results;
}

const files = walk(dir);
let modifiedCount = 0;

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;
    
    replacements.forEach(r => {
        content = content.replace(r.regex, r.replacement);
    });

    if (content !== original) {
        fs.writeFileSync(file, content, 'utf8');
        modifiedCount++;
        console.log('Modificado:', file);
    }
});

console.log(`\nReemplazo completado. Se modificaron ${modifiedCount} archivos.`);
