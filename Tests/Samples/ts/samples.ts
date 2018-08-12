function generic<T>(arg: T[]): T[] {
    console.log(arg.length);  // Array has a .length, so no more error
    return arg as T;
}

function normal(arg: string): string {
    return arg;
}

interface XYZ {
    test: test
}

let json = {
    // also 'string', keyword: return, and few others
    foo: test, // should not be matched as type
    number: 10,
    string: 'test',
    regex: /test/g,
    object: {
        nested: true
    },
    array: [ x, y, z ]
}

'test with as type in string'