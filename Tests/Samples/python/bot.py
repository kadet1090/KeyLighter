import discord
import asyncio
import re

client = discord.Client()

regex = {
    'score': re.compile('<@!?(.*?)>\s*\+\s*(\d+|\+)')
}


def get_score(score):
    return max(0, min(1 if score == '+' else int(score), 50))


def get_nick(member):
    return member.nick or member.name


def save_scores(scores, path):
    file = open(path, "w")
    file.write('\n'.join(['{} {}'.format(id, score) for id, score in scores.items() ]))
    file.close()


def load_scores(path):
    try:
        scores = {}

        file = open(path, "r")
        for line in file:
            (id, score) = line.split(' ')
            scores[id] = int(score)

        return scores
    except FileNotFoundError:
        return {}

@client.event
async def on_ready():
    print('Logged in as')
    print(client.user.name)
    print(client.user.id)
    print('------')


@client.event
async def on_message(message):
    matches = regex['score'].finditer(message.content)
    for match in matches:
        user, score = match.group(1), get_score(match.group(2))

        if message.author.id != user:
            try:
                scores[user] += score
            except KeyError:
                scores[user] = score

            print("User: {} += {} == {}".format(user, score, scores[user]))

        save_scores(scores, "scores.txt")

    matches = re.search('<@!?{}>\s+(\S+)(?:\s*(.*?))?'.format(message.server.me.id), message.content)
    if matches:
        try:
            command, args = matches.group(1), matches.group(2)

            print("C: {} A: {}".format(command, args))

            if command in ['stats', 'staty', 'stat']:
                await client.send_message(message.channel, '\n'.join(['{}: {}'.format(get_nick(message.server.get_member(id)), score) for id, score in scores.items()]))

        except AttributeError:
            pass


if __name__ == '__main__':
    scores = load_scores("scores.txt")
    client.run('psst, its a secret')