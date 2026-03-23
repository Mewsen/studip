STUDIP.ready(() => {
    const profileScoreContainer = document.getElementById('profile-score-container');
    if (profileScoreContainer) {
        const scoreTimeout = setTimeout(() => {
            STUDIP.jsonapi.GET(`users/${STUDIP.USER_ID}/score`).done((response) => {
                const hasScore = response?.score || false;
                const hasScoreTitle = response?.title || false;
                const hasScoreInfo = hasScore || hasScoreTitle;
                const hasKingIcons = response?.kings || false;
                if (hasScore) {
                    const profileScore = document.getElementById('profile-score');
                    profileScore.textContent = response.score;
                    profileScore.parentNode.classList.remove('hidden');
                }
                if (hasScoreTitle) {
                    const profileScoreTitle = document.getElementById('profile-score-title');
                    profileScoreTitle.textContent = response.title;
                    profileScoreTitle.parentNode.classList.remove('hidden');
                }
                if (hasKingIcons) {
                    const kingsContainer = profileScoreContainer.querySelector('.profile-score-kings');
                    response.kings.forEach(king => {
                        const kingImg = document.createElement('img');
                        kingImg.setAttribute('src', king.src);
                        kingImg.setAttribute('alt', king.alt);
                        kingImg.setAttribute('title', king.title);
                        kingImg.setAttribute('width', 42);
                        kingsContainer.append(kingImg);
                    });
                }
                if (hasScoreInfo || hasKingIcons) {
                    profileScoreContainer.querySelector('.profile-score-loader').classList.add('hidden');
                    profileScoreContainer.setAttribute('aria-busy', false);
                    if (hasKingIcons) {
                        profileScoreContainer.querySelector('.profile-score-kings').classList.remove('hidden');
                    }
                    if (hasScoreInfo) {
                        profileScoreContainer.querySelector('.profile-score-info').classList.remove('hidden');
                    }
                }
            });
            clearTimeout(scoreTimeout);
        }, 750);
    }
});
