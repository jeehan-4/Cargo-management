import fs from 'fs';
import zlib from 'zlib';
const b = fs.readFileSync('tests/visual/exports/report.pdf');
const s = b.toString('latin1');
console.log('header:', JSON.stringify(s.slice(0, 8)));
console.log('endsWithEOF:', s.trimEnd().endsWith('%%EOF'));
console.log('hasCatalog:', /\/Type\s*\/Catalog/.test(s));
console.log('pageObjects:', (s.match(/\/Type\s*\/Page[^s]/g) || []).length);
console.log('hasStartxref:', /startxref\s+\d+/.test(s));
console.log('embeddedFontFiles:', (s.match(/\/FontFile2?/g) || []).length);
let inflated = 0, textOps = 0, sample = '';
const re = /stream\r?\n/g; let m;
while ((m = re.exec(s))) {
  const start = m.index + m[0].length;
  const end = s.indexOf('endstream', start);
  if (end < 0) continue;
  try {
    const out = zlib.inflateSync(b.slice(start, end)).toString('latin1');
    inflated++;
    const tj = (out.match(/\)\s*Tj/g) || []).length;
    const TJ = (out.match(/\]\s*TJ/g) || []).length;
    textOps += tj + TJ;
    if (!sample && (tj + TJ) > 0) sample = out.slice(0, 160).replace(/[^\x20-\x7e]/g, '.');
  } catch (e) {}
}
console.log('inflatedStreams:', inflated, 'textShowOps:', textOps);
console.log('sampleContent:', sample);
