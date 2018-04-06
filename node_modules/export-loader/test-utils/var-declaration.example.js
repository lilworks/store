let variableDeclaration =  {
  "type": "VariableDeclaration",
  "declarations": [
    {
      "type": "VariableDeclarator",
      "id": {
        "type": "Identifier",
        "name": "data"
      },
      "init": {
        "type": "Literal",
        "value": "this is data",
        "raw": "'this is data'"
      }
    }
  ],
  "kind": "const"
}
module.exports = {
  variableDeclaration
}