const fs = require('fs');
const path = require('path');

const dir = 'd:/Documentos/Trabajo_Uni/ieeEdu/resources';

const replacements = [
    { regex: /\[#57572A\]/gi, replacement: 'primary' },
    { regex: /\[#FAFAF4\]/gi, replacement: 'background' },
    { regex: /\[#1A1A10\]/gi, replacement: 'on-background' },
    { regex: /\[#E7E6AB\]/gi, replacement: 'primary-fixed' },
    { regex: /\[#FBF9F2\]/gi, replacement: 'surface-dim' }, // FBF9F2 -> somewhat surface-dim
    { regex: /\[#f7f6f0\]/gi, replacement: 'surface-container' },
    { regex: /\[#f0efe8\]/gi, replacement: 'surface-container-highest' },
    { regex: /\[#CACA91\]/gi, replacement: 'primary-fixed-dim' },
    { regex: /\[#C9C7B8\]/gi, replacement: 'outline-variant' },
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
