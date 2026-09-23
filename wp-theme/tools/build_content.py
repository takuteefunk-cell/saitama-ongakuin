"""静的サイトの各ページ本文を WordPress 用に変換して pages.json に書き出す。"""
import json, re, pathlib

ROOT = pathlib.Path(__file__).resolve().parents[2]
OUT = pathlib.Path(__file__).resolve().parent / "pages.json"
PAGES = {
    "home": "index.html", "about": "about.html", "instructors": "instructors.html",
    "courses": "courses.html", "events": "events.html", "commission": "commission.html",
    "access": "access.html", "contact": "contact.html",
}
KEYS = {v[:-5]: k for k, v in PAGES.items()}
KEYS["index"] = "home"

CONTACT_FORM = """
<section>
  <div class="container">
    <div class="section-title">
      <span class="tag">Form</span>
      <h2>お問い合わせフォーム</h2>
      <p class="desc">24時間受け付けています。内容を確認のうえ、担当よりご連絡いたします。</p>
    </div>
    <div class="contact-form-wrap">
{{CF7}}
    </div>
  </div>
</section>
"""

def convert(key, html):
    title = re.search(r"<title>(.*?)</title>", html).group(1).split(" | ")[0]
    desc = re.search(r'<meta name="description" content="(.*?)">', html).group(1)
    body = html.split("</header>", 1)[1].split('<footer class="site-footer">', 1)[0].strip()
    body = re.sub(r'href="([a-z]+)\.html(#[^"]*)?"',
                  lambda m: f'href="?sao_page={KEYS[m.group(1)]}{m.group(2) or ""}"', body)
    body = body.replace('src="images/', 'src="sao-img/')
    if key == "contact":
        body = body.replace('<section class="alt">', CONTACT_FORM.strip() + '\n\n<section class="alt">', 1)
    if key == "home":
        title = "ホーム"
    return {"title": title, "excerpt": desc, "content": f"<!-- wp:html -->\n{body}\n<!-- /wp:html -->"}

data = {k: convert(k, (ROOT / f).read_text(encoding="utf-8")) for k, f in PAGES.items()}
OUT.write_text(json.dumps(data, ensure_ascii=False, indent=1), encoding="utf-8")
for k, v in data.items():
    left = re.findall(r'href="[a-z]+\.html', v["content"])
    print(k, v["title"], len(v["content"]), "unconverted:", left)
