<!--t How to Mirror Websites for Fun and Profit t-->
<!--d Mirroring, o cloning a website, is a great way to ensure the information from any site (or just parts of it) actually stays secure, protected against d-->
<!--tag Random tag-->

Mirroring, o cloning a website, is a great way to ensure the information from any site (or just parts of it) actually stays secure, protected against upstream damage of any kind. The practice can be used on either your own websites, or those of other people or organizations (with some caveats), and it can entail either making local copies for offline consultation and manual replication, or online clones for public benefit and immediate damage protection.

There are various tools made specifically for these purposes, and at the basis they could be used by basically anyone; case in point, I too was there downloading entire websites to my PC when I was 10 or so, since I wanted to be able to reference them even without Internet access, which at the time for me wasn't a daily given. And honestly there are only good reasons for mirroring websites that you care about, but nowadays the practice has more or less died, which is why I think everyone should at least know how to do this.

Keep in mind that this article aims to just give a quick and practical overview of the process of website mirroring to your computer, without going too much into the indirect technical details, or in the potential problems that could occur, both on your side and on those of the webmasters. Obviously, if you're both the mirroring user and the webmaster, I have nothing to warn you about, but in all other cases you must be mindful of how and where your target site is hosted, and known both if there any bot protections in place that could destroy your plan, and if the site admin is okay with you requesting such potentially big amounts of traffic in a very limited amount of time.
With that said, however, let's just see what is available for doing this kind of deed, and how to quickly get started.

## Mirroring Sites with GNU Wget

[Wget, specifically the GNU variant](https://www.gnu.org/software/wget/), is an extremely powerful web downloader. It's more or less the standard tool for various website mirroring tasks, and it's available on virtually any platform (and it is probably already installed on your system, if you use GNU+Linux). Being a big CLI program, it can be a bit intimidating, but this is why people like me exist: to read the huge boring-ass manual for the program (or, sometimes, just straight reading what other people like me have already written) and give you a synthesis.

In essence, following here is the most generally comprehensive Wget command that will take care of all small details for locally cloning a website. It's in extended form, with comments properly explaining what every flag does, and it can simply be copypasted in any UNIX shell. If you'd like a shortened version, well, make it yourself, because I can't be bothered, but if you're at that point you're probably an expert user just like me.

```sh{.pre}
wget \
  --mirror                      `# Download the full site(s)...`                                          \
  --no-parent                   `# ...but don't follow links above the specified path (if it's not root)` \
  --page-requisites             `# Get all assets (styles/scripts/images/...)`                            \
  --adjust-extension            `# Save files with the appropriate file extension`                        \
  --restrict-file-names=windows `# Ensure file names are compatible with both UNIX and Windows`           \
  --convert-links               `# Make URLs relative, making pages work outside of their domain`         \
  --retry-on-host-error         `# Retry if connection drops instead of skipping URLs`                    \
  --tries=inf                   `# Retry forever in case of temporary errors`                             \
https://thesite.com/whatever/path # The URL(s) to site(s) to download
```

```
## Example of mirroring spacccraft.altervista.org ##
--2025-06-09 18:05:34--  https://spacccraft.altervista.org/
Resolving spacccraft.altervista.org (spacccraft.altervista.org)... 176.9.79.149, 88.99.2.209, 94.130.164.5, ...
Connecting to spacccraft.altervista.org (spacccraft.altervista.org)|176.9.79.149|:443... connected.
HTTP request sent, awaiting response... 200 OK
Length: 54903 (54K) [text/html]
Saving to: 'spacccraft.altervista.org/index.html'
## ... 4 minutes of links later ... ##
FINISHED --2025-06-09 18:09:34--
Total wall clock time: 4m 0s
Downloaded: 445 files, 170M in 1m 49s (1.57 MB/s)
Converting links in ...
## ... ##
Converted links in 124 files in 8.6 seconds.
```

It's not the best thing in the entire world, but still this manages to download entire websites of medium size without strange issues and in not too much time, ensuring that all page resources are cloned, URLs adjusted, and connection issues (like the ones I constantly have on my PC...) are accounted for. The only real caveat here is that there doesn't seem to be any proper way to extend a mirror once it's finished, or if it's prematurely stopped, and instead a new one would need to be made from scratch.

## Mirroring Sites with HTTrack

An older program, strangely still kinda relevant today, is HTTrack. It describes itself as an offline browser, and is a mirroring tool mostly geared towards making local copies of websites, but it's in some ways more flexible than Wget, and with some advanced features being more easily accessible. It's offered in mainly 2 variants: WinHTTrack, the Windows variant with a native Win32 GUI, and WebHTTrack, the UNIX variant with a web control interface; oh, and an Android variant is also available.

HTTrack arguably has some important advantages over Wget that make it more favorable for larger-scale mirroring. Not only does it support multi-threading, meaning that more resources can be downloaded at the same time, thus speeding up the process, but it also always supports continuing stopped or finished download jobs, allowing for site mirrors to be extended when needed without redownloading everything.

As a GUI program that already comes with some default options, there isn't a lot that must be known about HTTrack before being able to start a mirroring job: just open it, paste links to the site's index page(s), and let it work. Some niche sites will require customizing some of the options, especially because the web has changed quite a lot in these last 8 years in which the tool hasn't ever seen a single new release, but it's not rocket science. Also, given that it supports continuing downloads despite of specific crawling or downloading configuration, it's in any case unlikely that you will need to throw an entire mirror away because of one mistake.

You can get HTTrack website copier at its official website (which maybe you can then clone to test, I don't know), where the documentation pages and the forum are also hosted: <https://www.httrack.com/>. Or, you can learn even more about it at these links:

* <https://en.wikipedia.org/wiki/HTTrack>
* *Copiare offline un intero sito web con HTTrack*: <https://www.trickit.it/linux/copiare-offline-un-intero-sito-web-con-httrack>

## Links and References

* <https://en.wikipedia.org/wiki/Mirror_site>
* *Keeping Your Site Alive: Mirroring your site*: https://www.eff.org/keeping-your-site-alive/mirroring-your-site
* *How do I completely mirror a web page?*: <https://stackoverflow.com/questions/400935/how-do-i-completely-mirror-a-web-page>
* *Using wget to copy website with proper layout for offline browsing*: <https://superuser.com/questions/970323/using-wget-to-copy-website-with-proper-layout-for-offline-browsing>
* *Local mirror of a website using wget?* <https://old.reddit.com/r/linuxmasterrace/comments/pst0cc/local_mirror_of_a_website_using_wget/>
* *Make Offline Mirror of a Site using `wget`*: <https://www.guyrutenberg.com/2014/05/02/make-offline-mirror-of-a-site-using-wget/>
* *Download an entire website with wget, along with assets*: <https://gist.github.com/crittermike/fe02c59fed1aeebd0a9697cf7e9f5c0c>
* *Mirroring a Website with Wget*: <https://kevincox.ca/2022/12/21/wget-mirror/>
* *How To Download A Website With Wget The Right Way*: <https://simpleit.rocks/linux/how-to-download-a-website-with-wget-the-right-way/>
* *wget -k converts files differently on Windows and Linux*: <https://stackoverflow.com/questions/629831/wget-k-converts-files-differently-on-windows-and-linux>
  * *How to serve a wget --mirror'ed directory of files with questionmarks in them*: <https://serverfault.com/questions/349673/how-to-serve-a-wget-mirrored-directory-of-files-with-questionmarks-in-them>
  * *"wget --restrict-file-names=windows" seems to fail converting links for NTFS*: <https://unix.stackexchange.com/questions/170923/wget-restrict-file-names-windows-seems-to-fail-converting-links-for-ntfs>
* *wget: come funziona l'utilità per scaricare file, anche da Windows*: https://www.ilsoftware.it/focus/wget-come-funziona-lutilita-per-scaricare-file-anche-da-windows/
* Wget for Windows: <https://memos.octt.eu.org/m/ekR8m9mDpyVEVPPzmd9DYP>

![](https://stuff.octt.eu.org/content/images/20250623230830-Untitled63284389.png)