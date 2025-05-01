document.addEventListener("DOMContentLoaded", () => {
    // DOM Elements
    const dom = {
        userSection: document.querySelector(".user-results-section"),
        leaderboardSection: document.querySelector(".leaderboard-section"),
        backButton: document.getElementById('backButton'),
        // We'll create other elements dynamically
    };

    // Sample data - in a real app, this would come from your backend/API
    const leaderboardData = {
        user: {
            id: 3,
            name: "JohnDoe",
            avatar: "J",
            score: 8,
            totalQuestions: 10,
            timeTaken: "4:32",
            rank: 2,
            questionResults: [
                { question: 1, correct: true },
                { question: 2, correct: false },
                { question: 3, correct: true },
                { question: 4, correct: true },
                { question: 5, correct: false },
            ]
        },
        participants: [
            { id: 1, name: "AlexJohnson", avatar: "A", score: 10, rank: 1 },
            { id: 2, name: "JaneSmith", avatar: "J", score: 9, rank: 2 },
            { id: 3, name: "JohnDoe", avatar: "J", score: 8, rank: 3 },
            { id: 4, name: "BobWilliams", avatar: "B", score: 7, rank: 4 },
            { id: 5, name: "SarahMiller", avatar: "S", score: 6, rank: 5 },
            { id: 6, name: "MikeBrown", avatar: "M", score: 5, rank: 6 },
        ]
    };

    // Initialize Leaderboard
    function initLeaderboard() {
        createUserSection();
        createLeaderboardSection();
        // Add back button functionality
        dom.backButton.addEventListener('click', () => {
            // In a real app, this would redirect to your home page
            window.location.href = '../'; // Change to your actual home page URL
            console.log('Redirecting to home page');
        });
    }

    // Create User Section Dynamically
    function createUserSection() {
        const user = leaderboardData.user;
        const accuracy = Math.round((user.score / user.totalQuestions) * 100);

        dom.userSection.innerHTML = `
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Results</h2>
            
            <div class="flex flex-col items-center mb-6">
                <div id="userAvatar" class="w-24 h-24 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-4xl mb-4">
                    ${user.avatar}
                </div>
                <h3 id="userName" class="text-xl font-semibold text-gray-800">${user.name}</h3>
                <p id="userRank" class="text-gray-500">Rank: ${user.rank}</p>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Score</span>
                    <span id="userScore" class="font-medium">${user.score}/${user.totalQuestions}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Accuracy</span>
                    <span id="userAccuracy" class="font-medium">${accuracy}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Time Taken</span>
                    <span id="userTime" class="font-medium">${user.timeTaken}</span>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-700 mb-3">Question Breakdown</h4>
                <div id="questionBreakdown" class="space-y-3">
                    ${user.questionResults.map((q, i) => `
                        <div class="flex justify-between items-center ${q.correct ? 'bg-green-50' : 'bg-red-50'} p-3 rounded-lg">
                            <span class="${q.correct ? 'text-green-700' : 'text-red-700'}">Q${i + 1}: ${q.correct ? 'Correct' : 'Incorrect'}</span>
                            <span class="${q.correct ? 'text-green-700' : 'text-red-700'}">${q.correct ? '+1' : '+0'}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    // ... (keep all the previous code until the createLeaderboardSection function)

    function createLeaderboardSection() {
        const topThree = leaderboardData.participants.slice(0, 3);

        // Reorder for proper podium display: 2nd, 1st, 3rd
        const podiumOrder = [topThree[1], topThree[0], topThree[2]];

        dom.leaderboardSection.innerHTML = `
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Leaderboard</h2>
        
        <!-- Top 3 Podium -->
        <div class="flex justify-center items-end gap-4 mb-8" id="topThreeContainer">
            ${podiumOrder.map((p, i) => {
            // Determine podium position
            let positionClass, heightClass, bgClass, textClass, avatarSize;

            if (p.rank === 1) {  // 1st place (center)
                positionClass = 'mx-2';
                heightClass = 'h-[220px]';
                bgClass = 'bg-purple-100';
                textClass = 'text-purple-700';
                avatarSize = 'w-20 h-20 text-2xl';
            } else if (p.rank === 2) {  // 2nd place (left)
                positionClass = 'mr-2';
                heightClass = 'h-[180px]';
                bgClass = 'bg-yellow-100';
                textClass = 'text-yellow-700';
                avatarSize = 'w-16 h-16 text-xl';
            } else {  // 3rd place (right)
                positionClass = 'ml-2';
                heightClass = 'h-[160px]';
                bgClass = 'bg-blue-100';
                textClass = 'text-blue-700';
                avatarSize = 'w-16 h-16 text-xl';
            }

            return `
                    <div class="${positionClass} ${heightClass} ${bgClass} w-1/4 rounded-t-lg p-4 flex flex-col items-center transition-all">
                        <div class="w-12 h-12 rounded-full ${bgClass} flex items-center justify-center ${textClass} font-bold mb-2">
                            ${p.rank}
                        </div>
                        <div class="${avatarSize} rounded-full ${p.rank === 1 ? 'bg-purple-200' : bgClass} flex items-center justify-center ${textClass} font-bold mb-2">
                            ${p.avatar}
                        </div>
                        <h3 class="font-medium text-center">${p.name}</h3>
                        <p class="text-sm text-gray-500">${p.score}/10</p>
                    </div>
                `;
        }).join('')}
        </div>
        
        <!-- Rest of Participants -->
        <div class="space-y-3" id="otherParticipants">
            ${leaderboardData.participants.slice(3).map(p => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <span class="w-8 text-center font-medium mr-4">${p.rank}</span>
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-bold mr-3">
                            ${p.avatar}
                        </div>
                        <span>${p.name}</span>
                    </div>
                    <span class="font-medium">${p.score}/10</span>
                </div>
            `).join('')}
        </div>
    `;
    }

    // ... (rest of the code remains the same)

    // Initialize
    initLeaderboard();
}); 