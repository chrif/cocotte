# Create a release

```
git checkout master
git pull
git fetch -p -P
docker run -it --rm -v $(pwd):/usr/local/src/your-app ferrarimarco/github-changelog-generator -u chrif -p cocotte
git tag -a <version> -m $(cat CHANGELOG.md)
git push --tags
```
