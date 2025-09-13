<!--t The failed attempt of running HaxeFlixel on the Switch t-->
<!--d After having tried and succeeded in making my first game with HaxeFlixel, partly to test the engine and partly because I have some extensive gaming d-->
<!--tag Random tag-->

[After having tried and succeeded in making "my" first game with HaxeFlixel](https://octospacc.altervista.org/2025/08/29/mikulogicoso-inufficiale-programmato-con-lasciaflissa-rilascio-mikulogi-octt-unofficial-edition/), partly to test the engine and partly because I have some extensive gaming plans, the problem now comes to having it run on as many platforms as possible. Shameless self-promotion here, obviously, but go try the game if you like Picross... even though what I created is really just a straight clone of an existing game, and still in a very early stage: <https://octt.itch.io/mikulogi-octt>.

This said, this post is not really about my specific game, but more about running potentially any HaxeFlixel game on the Nintendo Switch console, via homebrew means... or, more exactly, what I thought was a genius breakthrough that immediately revealed itself to be nothing (or, actually, worse than nothing, because I wasted half a day on this). And okay, it might seem senseless to write about an utter failure, but I figure it's better to write down what I've still done and know for a fact that it doesn't work, so that maybe in the future someone could help in finding a solution... or, at least, avoid wasting their own time too.

## For the memes: the web browser applet

I knew this one way wouldn't have worked — and, even if it had, it would have ran so incredibly slowly that it wouldn't really be a solution, not to mention how clunky it would be to use — but I just had to try it. I opened my itch.io page in [the secret web browser of my Switch](https://memos.octt.eu.org/m/ixH7uufW3gjswPKbxmDcaq) (available on unmodded consoles too via some tricks, but I just used [the BrowseNX homebrew](https://memos.octt.eu.org/m/CZgVg8YivCMXa6BxExQUcq) to launch it), hoping the HTML5 build of my game would run... but, obviously, it doesn't. I even tried opening the iframe URL directly, thinking that maybe itch.io was interfering with the browser... but no, same thing: there's only a blank page. HaxeFlixel (or well, its dependencies really) clearly uses JavaScript code requiring features that the restricted WebKit-based browser of the Switch doesn't offer, tough luck.

## For the actual attempt: nx.js

Ok, time for jokes is up, and now it was the moment to think about actual solutions. The first possible one that came to my mind was to use a nifty piece of library I had found some time ago, that being [nx.js](https://memos.octt.eu.org/m/Z7WfAe7gdPChmGCqEQvCFB), a runtime and toolchain for making Nintendo Switch homebrew apps using JavaScript (or WASM). For it to magically work was out of the question, since, while it comes with a modern JavaScript runtime based on [the QuickJS engine](https://memos.octt.eu.org/m/S5PD688bt2Pp6Uw5exkUu9), and with a built-in 2D [Canvas API](https://memos.octt.eu.org/m/Hh4J2HZB5cpWbE2MgEwRyK) for drawing graphics to the screen, it's not a full browser environment, and it has no DOM or any other tech any standard HTML5 game could expect to be available...

And so, while I knew that work on this couldn't have been easy, I was at least hoping for it not to be impossible, since I figured stuff would have been able to run with some tweaking. Long story short, this obviously wasn't the case, since the most I ended up with was a screen not filled with errors, but just completely blank (black).

After getting one of the nx.js examples on my PC, I compiled and then ran it on my Switch (I had no luck in running it on emulators, which I would have liked for faster testing) to make sure that the homebrew itself was working, and it was. Then, I just built it with my JavaScript file straight from HaxeFlixel instead, to see what it would have done. Now, in similar situations, some would say "_talk is cheap, show me the code_"... and okay, if it's really hacky, broken and literally non-functioning code (or, well, patching procedures more specifically) that you want, here you go. It's a matter of seeing error traces, and then slowly work at to mining them away, by going to the specified line of code inside the gigantic single file and patching the expression that threw the error in each case.

Before the actual suffering started, tough, the first thing needed was to copy the bootup line from the HTML file to the JavaScript, because nx.js doesn't run HTML, and instead directly runs the JavaScript file. The line in question is the following, and I took it from the near end of the HTML to the end of the JavaScript:

```js
lime.embed ("GameName", "openfl-content", 640, 480, { parameters: {} });
```

For the very first error I then encountered, the problem is strangely with some of the third-party libraries embedded in the JavaScript, in this case LZMA and probably another thing. As long as the game doesn't actually require them, though, they can actually be commented out, and the app will continue to work. So, near the end of the file, I commented out the lines that start as follows:

```js
// if(typeof self === "undefined" || !
// if(n+2>=i)return e;if(t=255&e[++n],
// if(typeof self === "undefined" || !
// var saveAs=saveAs||function(e){"use
//  }
```

And now, the rest of the patches are ordered a bit as I feel like: partly in the order of discovery of things, but partly in order of physical appearance in the file, because I've already forgot some of my steps. Not that it matters, since they are useless anyway as they are, so it's already unlikely enough that someone will actually try and replicate them (but, if you do without throwing up, please leave a comment, idk).

So, for this very next one, search for the `getBrowser` function and edit its body to short-circuit all the checks to unknown. See below the lines I deactivated to achieve this:

```js
	,getBrowser: function() {
		// if(this.userAgentContains(" OPR/")) {
		// 	return flixel_system_frontEnds_FlxBrowser.OPERA;
		// } else if(this.userAgentContains("chrome",true)) {
		// 	return flixel_system_frontEnds_FlxBrowser.CHROME;
		// } else if($global.navigator.appName == "Netscape") {
		// 	return flixel_system_frontEnds_FlxBrowser.FIREFOX;
		// } else if(document.documentMode) {
		// 	return flixel_system_frontEnds_FlxBrowser.INTERNET_EXPLORER;
		// } else if(Object.prototype.toString.call(window.HTMLElement).indexOf("Constructor") > 0) {
		// 	return flixel_system_frontEnds_FlxBrowser.SAFARI;
		// }
		return flixel_system_frontEnds_FlxBrowser.UNKNOWN;
	}
```

Then, also look for a function deep in the file called `lime__$internal_backend_html5_HTML5Window`, and inside its body, about 60 lines below the start, you could find an `if` block which, for some reason, differently from the many other similar ones inside the same function, has a simple else condition. Modify it to be like follows:

```js
if(this.canvas != null) {
...
// } else {
} else if (this.div != null) {
...
}
```

```js
	if(this.canvas != null) {
		this.canvas.width = ...
		this.canvas.height = ...
		this.canvas.style.width = ...
		this.canvas.style.height = ...
	// } else {
	} else if (this.div != null) {
		this.div.style.width = ...
		this.div.style.height = ...
	}
```

Also search for a `setCursor` function, inside the `lime__$internal_backend_html5_HTML5Window.prototype` object, and, at the very initial boolean check, modify the line to check for if one variabile is actually defined (because on nx.js it won't be):

```js
	,setCursor: function(value) {
		// if(this.cursor != value) {
		if(this.cursor != value && this.parent.element) {
...
```

<!--
With these said, the next patches will appear strictly in order of physical appearance in the file, not the order of discovery, because I've already forgot some. Not that it matters, since they are useless anyway as they are, so it's already unlikely enough that someone will actually try and replicate them (but, if you do without throwing up, please leave a comment, idk).
-->

![Misc OpenFL errors on the Switch](https://stuff.octt.eu.org/content/images/20250831152951-2493943943Capture.PNG)

Continuing on, to allow the game to still work on a normal web browser for testing with my PC if things broke or not, the behavior of some expressions had to be modified to account for nx.js, while behaving the same on a browser, and this is done with a simple boolean check for the presence of the `Switch` global object, which is nx.js-specific and not present in any web browser. Nice!

As the first thing with this method, search for the substring `instanceof HTMLCanvasElement`, which are part of some `if` expressions (2 for me), and at the start of the expression add a shortcircuiting OR checking for if the Switch object is defined:

```js
		// } else if(((value) instanceof HTMLCanvasElement)) {
		} else if(window.Switch || ((value) instanceof HTMLCanvasElement)) {
```

```js
  // if(((element) instanceof HTMLCanvasElement)) {
  if(window.Switch || ((element) instanceof HTMLCanvasElement)) {
```

Now, go to the `lime__$internal_graphics_ImageCanvasUtil.createCanvas` function, and inside edit the line that creates a new canvas to just return the only canvas available when on nx.js, which is the global `screen` object:

```js
		// buffer.__srcCanvas = window.document.createElement("canvas");
		buffer.__srcCanvas = window.Switch ? window.screen : window.document.createElement("canvas");
```

Finally for this section, look for the application init function, named `lime_system_System.embed` or `$hx_exports["lime"]["embed"]`, and enclose all the small `if` blocks that have something to do with an HTML element to only run when not on Switch, otherwise the initialization will fail (not due to an error, but as technically intended):

```js
		if (!window.Switch) { // parent check added!
			if(typeof(element) == "string") {
				htmlElement = window.document.getElementById(element);
			} else if(element == null) {
				htmlElement = window.document.createElement("div");
			} else {
				htmlElement = element;
			}
			if(htmlElement == null) {
				window.console.log("[lime.embed] ERROR: Cannot find target element: " + Std.string(element));
				return;
			}
		}
```

Another issue now was that the OpenFL assets loader requires [the crazy old XMLHttpRequest API](https://memos.octt.eu.org/m/FCjoKARZWomUq89NgM7Qha), which isn't included on nx.js, but we have [the Fetch API](https://memos.octt.eu.org/m/Z2AwZCwZZvmfs8aRaekNJ2) at least. That's why I thought of using [xhr-shim](https://github.com/apple502j/xhr-shim) to basically polyfill the former with the latter... but, after adding the library to the JavaScript, the game actually stopped working, even on PC; more on this later.

To fix yet another error finally, thrown due to the lack of the global Location object, but some code reading the href property, stub it by adding this line to the very top of the JavaScript:

```js
if (!window.location) window.location = {};
```

In actuality I would have liked to have been able to just stub everything that was missing, instead of using flaky manual patches, but I quickly wound up at a dead end of incomprehensible errors when I tried. Maybe I haven't tried hard enough, but could you be bothered if you were in my shoes? Anyways, just for reference, this is the rest of my stub code that I then had to throw out as quickly as I had written it.

```js
['document', 'location'].forEach(key => {
	if (!(key in window)) {
		window[key] = {};
	}
});
if (!('HTMLCanvasElement' in window)) {
	window.HTMLCanvasElement = class HTMLCanvasElement {
		static [Symbol.hasInstance](obj) {
			return (obj === screen);
		}
	}
}
if (!document.createElement) {
	document.createElement = function createElement(tag){
		return (tag === 'canvas' ? Object.assign(screen, fakeStyle()) : fakeElement());
	};
}
if (!document.getElementById) {
	document.getElementById = function getElementById(){
		return fakeElement();
	};
}
function fakeElement() {
	return { ...fakeStyle(), addEventListener(){}, removeEventListener(){}, createContext(){} };
}
function fakeStyle() {
	return { style: { setProperty(){} } };
}
```

And now, the game actually doesn't crash with an error! But that's not a good thing, because it doesn't play either... and, since nx.js seems to have no debugger, apart from [the standard JavaScript Console API](https://memos.octt.eu.org/m/QQiT7vUTxtCh5sTCh5i45a) to write text to the screen or to a debug log file, this is a big problem, because it's practically impossible (short of writing millions of print statements, at least) to know where the code trips up.

![Showing the white square on the Switch](https://stuff.octt.eu.org/content/images/20250831153209-2493943943C1apture.PNG)

Indeed, what I saw at this point was... just a small white square near the top left of the screen. And, as much as it pains me to say, and knowing full well that this revelation will probably make 99% of people who haven't yet closed this piece of crap article do so immediately now, things did not get better from this. In any case at this point, even if things looked promising, the game was indeed still not running, because it was probably stuck at assets failing to load via the XHR shim, just like on PC (but on that, at the very least, it at least showed a frozen HaxeFlixel preloader screen, not this!).

<!--
Before closing, I have to mention that, initially, I tried going straight into trying to make my game work, but I quickly found an issue where loading of assets couldn't possibly have worked, because of OpenFL's complex asset loading mechanism complicating things. First of all, it uses the XMLHttpRequest API, which isn't available on nx.js... but, the Fetch API is available, and so I immediately tried using [xhr-shim](https://github.com/apple502j/xhr-shim) to work around it... but, sadly, I couldn't get it to work; with this library included in the JavaScript, the game stopped loading even on PC, so this is definitely not the road I wanted to go down. But, the crazy thing is that... this got me at least a white square up in the top left corner of my Switch. Unexplainable, unhelpful, and not to mention rage-inducing, seeing that the other game didn't even show this, but this is where my _devrotting_ ends for now.
-->

Seeing this, I decided to put aside my specific game for the moment, and switch to one that doesn't require any assets from files, so that I could see if there was anything beyond this point. I chose the HaxeFlixel Breakout demo, and what has already been said holds up, but from here more patches stem.

First, while this small game doesn't require file assets, HaxeFlixel itself does. Mainly to show the intro, but that can be disabled when initializing the `FlxGame` object in the game's code (and I think it must actually be disabled, when going to do what follows). The actual issue is that the OpenFL asset loader seems to be hardcoded to run even if no assets need to be loaded, in a way that isn't simply modifiable via HaxeFlixel, but this was still easy to work around: near the end of the `ApplicationMain.create` function, I disabled the line that runs the preloader, and immediately at the end of the current function I call the `ApplicationMain.start` function to start the actual game:

```js
	// app.__preloader.load();
	var result = app.exec();
	ApplicationMain.start(stage); // new entrypoint added here!
```

Using the web browser on my PC, I verified that this actually works: now the game starts immediately, without the HaxeFlixel preloading screen. Fun fact: the game now runs without a web server, even by loading the HTML file directly via `file:///`... knowing this could be useful even in less crazy situations!

Surprisingly, another runtime error now appears on the Switch, and it has to do with the game trying to use hardware acceleration where there is none. I believe this can be changed by just setting the property `hardware="false"` on the `<window />` element in the `Project.xml` game file, but I didn't want to recompile the JavaScript and lose all my precious manual changes, so I just changed it in the JavaScript. Again in `ApplicationMain.create`, I just edited the `hardware` property inside the `attributes.context` JSON object to be `false`, and this solved the error.

And with this, however, we're back at square zero. And I mean 0 as in absence of a square, unlike previously, because now the screen is completely blank (black) without errors. With the smaller game still working in a proper web browser, but not on nx.js, I officially have no more clues of where to go from there, and have to admit defeat under the weight of my own gaming ambitions.

## Future art

The reality of this world, defined by so much incompatibility in software, hurts me greatly. But, with this I've only tried one way of accomplishing this useless goal of mine. Well, maybe not so useless: there were people wanting to port _Friday Night Funkin'_ to Switch as a homebrew, but shit was so impossible that they soon gave up on the original code, and instead rewrote the entire game in LOVE2D to make it run on the flimsy console... you get the point. The thing is, there are technically other very sensible ways trough this could be accomplished, although none is really fun:

+ Tweak the OpenFL/Lime code regarding JavaScript output — or even better, WebAssembly, because seeing how slow a JS HaxeFlixel build runs on PC and smartphones, and given that the Switch is much worse than those, I wouldn't really hope for it to perform that well otherwise — so that dependency on a browser environment is eradicated, and then it should just work in nx.js.
+ Find a way to compile the game(s) targeting SDL2 (which is part of libnx) trough C++, by leveraging the HXCPP code emitter; This would be the actually correct way, similar to how HaxeFlixel currently compiles for (some of the) supported native platforms, but something tells me that it's a lot more difficult than just passing the Haxe-generated C++ code over to the devkitpro toolchain.
+ Write wrapper code that replicates the HaxeFlixel API when targeting a platform not supported by Lime, such that the game code doesn't need to be changed, but uses another game engine behind the scenes, such as LOVE2D (damn) or some known-working C/C++ framework... Yeah, not elegant, but [someone has actually already done this in production, but targeting Unity](https://massivemonster.com/blog/the-adventure-pals-porting-to-console-via-unity-part-1) (except they wrote the game in OpenFL, so 1 layer of abstraction below HaxeFlixel), so it's not horrible.

As far as I'm concerned, this latest _devrotting_ adventure ends here, for now. It's not to exclude that I could myself try the other two options in the future but, currently, I'm actually having issues with compiling my HaxeFlixel game for just Android, which is supported by Lime but, as always with Android, it doesn't just work... and this issue is of much higher priority than getting the game to run on puny consoles. (I swear to god, if I end up having to rewrite it in Love2D I will transform this entire planet into dust...)