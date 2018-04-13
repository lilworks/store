# import-loader [![npm version](https://badge.fury.io/js/import-loader.svg)](http://badge.fury.io/js/import-loader)
Webpack loader to import files at once with glob

# It's just a concept yet

## Install

```
npm install --save-dev import-loader
```
or
```
yarn add --dev import-loader
```

## How to use

Quick
```js
import models from 'import?glob=models/*.js'
```

### Use `?glob`

```js
var models = require('import-loader?glob=models/*.js')
// or
var models = require('import?glob=models/*.js')
// or
import models from 'import?glob=models/*.js'
```


It will be something like that:
```js
var models ={
  'models/User.js': require('models/User.js'),
  'models/Pet.js': require('models/Pet.js'),
}
```

### Use `?name`

`?name=basename`

```js
var models = require('import?glob=models/*.js&name=basename')
// Its will like that
var models = {
  User: require('models/User.js'),
  Pet: require('models/Pet.js'),
}
```

`?name=path`

```js
var models = require('import?glob=models/*.js&name=path')
// Its will like that
var models ={
  'models/User.js': require('models/User.js'),
  'models/Pet.js': require('models/Pet.js'),
}
```


### Use `?dir` and `?ext`

If you have flat list of files in directory, you can drequire like that:

```js
import models from 'import?dir=models'
// or
import models from 'import?dir=models&ext=.server.js'
// or
import components from 'import?dir=components&ext=jsx'
```

#### Notes
1. By default `?ext=js`
2. In `?dir` mode by default `?name=basename` (if you want path, you need `?name=path`)

### Use glob `.pattern` file

You can write your glob pattern  If you have a large pattern file

1. Create a glob pattern file (example: `models.pattern`)
```
./models/*.js
```

2. Use glob file
```js
var models = require('import!./models.pattern');
```

Note:
Pattern is relative to the file directory.

## Other Examples


## TODO

* source map
