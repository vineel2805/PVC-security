const fs = require('fs');
const path = require('path');

const projectRoot = path.join(__dirname, '..');
const auditDir = __dirname;

const allowedExts = ['.php', '.css', '.js', '.html', '.sql', '.json', '.md'];
const excludeDirs = ['.git', 'node_modules', 'vendor', 'audit'];

let fileStats = {
    total: 0,
    byExtension: {}
};

let allFiles = [];

function walkDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        if (stat.isDirectory()) {
            if (!excludeDirs.includes(file)) {
                walkDir(fullPath);
            }
        } else {
            const ext = path.extname(file).toLowerCase();
            if (allowedExts.includes(ext) || file.toLowerCase() === 'makefile') {
                allFiles.push(fullPath);
                fileStats.total++;
                fileStats.byExtension[ext] = (fileStats.byExtension[ext] || 0) + 1;
            }
        }
    }
}

walkDir(projectRoot);

console.log(JSON.stringify(fileStats, null, 2));

// Save file inventory
fs.writeFileSync(path.join(auditDir, 'file-inventory.json'), JSON.stringify(allFiles, null, 2));
