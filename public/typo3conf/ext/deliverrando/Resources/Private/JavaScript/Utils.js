const deescapeHtml = (text) =>
{
    var map = {
        '%5B': '[',
        '%5D': ']',
        '%3F': '?',
    };

    return text.replace(/%5B|%5D|%3F/g, (m) => { return map[m]; });
};