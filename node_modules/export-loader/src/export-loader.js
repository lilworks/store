const { generateExports } = require('./esprima')
const d                   = require('debug')('norequire')
const esprima             = require('esprima')
const { readFileSync }    = require('fs')
const path                = require('path')

// main loader func, calls generateExports with dependencies
// which returns a func with a parser inside
function exportLoader(content) {
  try{
    // get current path from context or resolve when testing
    let currentPath = this.resourcePath || path.resolve("./test-utils/resourcePath.js")
    // get finalExports after parsing
    let { finalExports } = generateExports(esprima)(currentPath, readFileSync(currentPath,'utf8'))
    d(`finalExports is: ${finalExports}`)
    // replace all existing exports for duplicate error prevention
    content = content
      .replace(/export function/g,'function')
      .replace(/export (let|const|var)/g,'let')
      .replace(/export async function/g,'async function')
      .replace(/^export\s*{([\s\S]*?)}$/gm, '')
      .concat('\n')
      .concat(finalExports)

    d(content)
  }catch(e){
    d(`Failed on export loader main func with error:${e}`)
  }
  return content
}

module.exports = exportLoader;
module.exports.default = exportLoader;

