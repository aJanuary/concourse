# Rewrites each saved page as one tag or run of text per line, with
# attributes sorted and every entity decoded, for tools/snapshot/compare
import html
import html.parser
import os
import re
import sys

VOLATILE = re.compile(r'^(csrf_token|token)$')
# CSRF tokens and session ids, wherever they appear
HEX = re.compile(r'\b[0-9a-f]{64}\b')


class Normaliser(html.parser.HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.lines = []
        self.raw = 0

    def attrs(self, attrs):
        names = dict(attrs)
        out = []
        for name, value in sorted(attrs, key=lambda a: a[0]):
            if name == 'value' and VOLATILE.match(names.get('name') or ''):
                value = '…'
            out.append(name if value is None else f'{name}="{value}"')
        return (' ' + ' '.join(out)) if out else ''

    def handle_starttag(self, tag, attrs):
        self.lines.append(f'<{tag}{self.attrs(attrs)}>')
        if tag in ('script', 'style'):
            self.raw += 1

    def handle_startendtag(self, tag, attrs):
        self.handle_starttag(tag, attrs)

    def handle_endtag(self, tag):
        self.lines.append(f'</{tag}>')
        if tag in ('script', 'style'):
            self.raw -= 1

    def handle_data(self, data):
        if self.raw:
            lines = [l.strip() for l in data.splitlines() if l.strip()]
            self.lines.extend(lines)
            return
        text = ' '.join(data.split())
        if text:
            self.lines.append(text)

    def handle_comment(self, data):
        self.lines.append(f'<!--{" ".join(data.split())}-->')

    def handle_decl(self, decl):
        self.lines.append(f'<!{decl.lower()}>')


source, dest = sys.argv[1], sys.argv[2]
for name in sorted(os.listdir(source)):
    with open(os.path.join(source, name), encoding='utf-8', errors='replace') as f:
        parser = Normaliser()
        parser.feed(f.read())
        parser.close()
    with open(os.path.join(dest, name), 'w', encoding='utf-8') as f:
        f.write(HEX.sub('…', '\n'.join(parser.lines)) + '\n')
