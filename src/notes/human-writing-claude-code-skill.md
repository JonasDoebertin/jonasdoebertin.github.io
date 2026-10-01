---
title: "human-writing: a Claude Code skill that cuts AI slop and keeps the meaning"
description: "human-writing is a Claude Code skill that cuts text to the length its reader needs and removes AI writing patterns. It combines concise-writing, stop-slop, and humanizer into one skill."
date: 2026-10-01
---

<p class="lead">Text from a language model has a sound you learn to recognize: the opener that announces the point, the "not X, but Y" reversal, three adjectives where one would do, the last line that repeats the paragraph. Several Claude Code skills strip these patterns out. I combined three of them into one skill, human-writing, and made it the first skill in my Claude Code plugin.</p>

## What human-writing does

The skill fixes two problems: text that is longer than its job requires, and text that reads as machine-written.

It first pins down what the text is for and who reads it. You can't judge "long enough" without knowing that. Then it cuts from the top down: whole sections and sentences first, then restatements, then whatever the context already says, and single words last. After that it checks the text against a catalog of AI tells (filler phrases, formulaic contrasts, false agency, mechanical rhythm, em dashes) and rewrites each flagged sentence around the claim it was hiding.

A qualifier that keeps a claim true stays, so "most teams struggle" doesn't turn into "teams struggle". The skill also never adds a fact, name, or number the source doesn't have. If a sentence needs a detail that's missing, it asks you for it or writes a simpler sentence.

When it edits your text, you get back the result, a before/after word count, a score out of 50, and a short note on which cuts did the most work.

It works in whatever language you write in. The tell catalog is in English, so for other languages the skill looks for the local versions of the same patterns.

## An example

The worked run in the repository edits an AI-written product update email. The draft opened like this:

> We're absolutely thrilled to share some exciting news with you! At [Company], we've always believed that great products are a testament to listening to our users, and this release marks a pivotal moment in that ongoing journey.

It went on for 218 words, with emoji bullets and a sign-off about exciting times ahead. This is the version human-writing produced:

> Subject: Imports are faster, and large files now work
>
> Hi there,
>
> Two changes in this release:
>
> - Imports run faster.
> - Large files no longer fail partway through. If an import used to time out, try it again.
>
> Nothing on your end to do. Reply if anything looks off.
>
> The [Company] Team

That's 54 words, and it tells customers more than the original did: large files no longer fail, and they don't have to do anything. Neither fact was in the draft; the user supplied both.

## How is it different from humanizer and stop-slop?

It merges both with a third skill, concise-writing, and decides what happens when their rules conflict.

- [concise-writing](https://github.com/l4ci/skills/tree/main/plugins/stray/skills/concise-writing) by Volker Otto brings the procedure: the cut ladder and the guardrail against over-trimming.
- [stop-slop](https://github.com/hardikpandya/stop-slop) by Hardik Pandya supplies the catalog of slop phrases and structures and the five-part score.
- From [humanizer](https://github.com/blader/humanizer) by Siqi Chen come the patterns you only see in the whole document, such as text that describes itself or a reply that re-explains what the reader already knows. Humanizer also supplies the rule against inventing facts.

Put together, some of their rules pull in opposite directions. The slop rules say to cut the adverb and the hedge, while concise-writing says to keep a qualifier that keeps a claim true. human-writing lets meaning win, so the qualifier stays. If you want the slop rules applied as hard bans, ask for the strict version.

## Install

The skill ships with my plugin `dieserjonas`. The repository is its own marketplace, so two commands in Claude Code install it:

```text
/plugin marketplace add JonasDoebertin/skills
/plugin install dieserjonas@dieserjonas
```

Claude loads the skill when you ask it to tighten a text or make it sound less like AI. To call it directly, use `/dieserjonas:human-writing`.

More of my skills will land in the same plugin, and `/plugin marketplace update dieserjonas` pulls them in. The source is on [GitHub](https://github.com/JonasDoebertin/skills).
