document.addEventListener("DOMContentLoaded", () => {
    // DOM Elements
    const dom = {
        userSection: document.querySelector(".user-results-section"),
        leaderboardSection: document.querySelector(".leaderboard-section"),
        backButton: document.getElementById('backButton'),
        participantModal: document.getElementById('participantModal'),
        modalContent: document.getElementById('modalContent'),
        closeModal: document.getElementById('closeModal'),
        toggleHostView: document.getElementById('toggleHostView'),
        hostControls: document.getElementById('hostControls')
    };

    // Set host view if user is host
    if (isHost) {
        document.body.classList.add('host-view');
    }

    // Initialize Leaderboard
    function initLeaderboard() {
        fetchLeaderboardData()
            .then(data => {
                createUserSection(data);
                createLeaderboardSection(data);
                setupEventListeners();
            })
            .catch(error => {
                console.error('Error loading leaderboard:', error);
                dom.userSection.innerHTML = `<p class="text-red-500">Error loading leaderboard data</p>`;
                dom.leaderboardSection.innerHTML = `<p class="text-red-500">Error loading leaderboard data</p>`;
            });
    }

    // Fetch leaderboard data from server
    async function fetchLeaderboardData() {
    try {
        const response = await fetch(`../backend/leaderboardBackend.php?session_id=${sessionId}&quiz_id=${quizId}&user_id=${userId}&host_id=${hostId}&ajax=1`);
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.status}`);
        }
        const data = await response.json();
        console.log('Fetched data:', data); // Debug log
        if (data.error) {
            throw new Error(data.error);
        }
        return data;
    } catch (error) {
        console.error('Error fetching leaderboard data:', error);
        throw error;
    }
}

    // Set up event listeners
    function setupEventListeners() {
        // Back button
        dom.backButton.addEventListener('click', () => {
            window.location.href = '../';
        });

        // Modal close button
        dom.closeModal.addEventListener('click', () => {
            dom.participantModal.classList.add('hidden');
        });

        // Toggle host view
        if (dom.toggleHostView) {
            dom.toggleHostView.addEventListener('click', () => {
                document.body.classList.toggle('host-view');
            });
        }
    }

    // Create User Section Dynamically
    function createUserSection(data) {
        const user = data.user;
        const accuracy = Math.round((user.score / user.totalQuestions) * 100);

        dom.userSection.innerHTML = `
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Results</h2>
            
            <div class="flex flex-col items-center mb-6">
                <div class="w-24 h-24 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-4xl mb-4">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <h3 class="text-xl font-semibold text-gray-800">${user.name}</h3>
                <p class="text-gray-500">Rank: ${user.rank}</p>
            </div>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Quiz</span>
                    <span class="font-medium">${data.quizTitle}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Score</span>
                    <span class="font-medium">${user.score}/${user.totalQuestions}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Accuracy</span>
                    <span class="font-medium">${accuracy}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Time Taken</span>
                    <span class="font-medium">${user.timeTaken || 'N/A'}</span>
                </div>
            </div>
            
            ${user.questionResults && user.questionResults.length > 0 ? `
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-700 mb-3">Question Breakdown</h4>
                <div class="space-y-3">
                    ${user.questionResults.map((q, i) => `
                        <div class="flex justify-between items-center ${q.correct ? 'bg-green-50' : 'bg-red-50'} p-3 rounded-lg">
                            <div>
                                <span class="${q.correct ? 'text-green-700' : 'text-red-700'} font-medium">Q${i + 1}:</span>
                                <span class="text-gray-700 ml-2">${q.question || 'Question ' + (i + 1)}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="${q.correct ? 'text-green-700' : 'text-red-700'} mr-3">${q.timeSpent || 'N/A'}</span>
                                <span class="${q.correct ? 'text-green-700' : 'text-red-700'} font-medium">${q.correct ? '+1' : '+0'}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}
        `;
    }

    function createLeaderboardSection(data) {
        const topThree = data.participants.slice(0, 3);
        const otherParticipants = data.participants.slice(3, 10); // Get top 10

        // Reorder for proper podium display: 2nd, 1st, 3rd
        const podiumOrder = topThree.length >= 3 ? [topThree[1], topThree[0], topThree[2]] : topThree;

        dom.leaderboardSection.innerHTML = `
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Leaderboard</h2>
                <span class="text-gray-500">Top ${data.participants.length} Participants</span>
            </div>
            
            ${topThree.length > 0 ? `
            <!-- Top 3 Podium -->
            <div class="flex justify-center items-end gap-4 mb-8">
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
                        <div class="${positionClass} ${heightClass} ${bgClass} w-1/4 rounded-t-lg p-4 flex flex-col items-center transition-all cursor-pointer hover:shadow-md" 
                             data-id="${p.id}" onclick="showParticipantDetails(${p.id})">
                            <div class="w-12 h-12 rounded-full ${bgClass} flex items-center justify-center ${textClass} font-bold mb-2">
                                ${p.rank}
                            </div>
                            <div class="${avatarSize} rounded-full ${p.rank === 1 ? 'bg-purple-200' : bgClass} flex items-center justify-center ${textClass} font-bold mb-2">
                                ${p.name.charAt(0).toUpperCase()}
                            </div>
                            <h3 class="font-medium text-center">${p.name}</h3>
                            <p class="text-sm text-gray-500">${p.score}/${p.totalQuestions || 10}</p>
                            <p class="text-xs text-gray-400 mt-1">${p.timeTaken || 'N/A'}</p>
                        </div>
                    `;
                }).join('')}
            </div>
            ` : ''}
            
            <!-- Rest of Participants -->
            <div class="space-y-3">
                ${otherParticipants.map(p => `
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer" 
                         data-id="${p.id}" onclick="showParticipantDetails(${p.id})">
                        <div class="flex items-center">
                            <span class="w-8 text-center font-medium mr-4">${p.rank}</span>
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-bold mr-3">
                                ${p.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div>${p.name}</div>
                                <div class="text-xs text-gray-500">${p.timeTaken || 'N/A'}</div>
                            </div>
                        </div>
                        <span class="font-medium">${p.score}/${p.totalQuestions || 10}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    // Show participant details (for host view)
    window.showParticipantDetails = function(participantId) {
        if (!isHost) return;
        
        fetch(`../backend/leaderboardBackend.php?user_id=${participantId}&session_id=${sessionId}`)
            .then(response => response.json())
            .then(participant => {
                if (!participant) return;

                document.getElementById('modalParticipantName').textContent = `${participant.name}'s Performance`;
                
                dom.modalContent.innerHTML = `
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-gray-500 text-sm">Rank</div>
                            <div class="font-bold text-xl">${participant.rank}</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-gray-500 text-sm">Score</div>
                            <div class="font-bold text-xl">${participant.score}/${participant.totalQuestions}</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-gray-500 text-sm">Time Taken</div>
                            <div class="font-bold text-xl">${participant.timeTaken || 'N/A'}</div>
                        </div>
                    </div>
                    
                    ${participant.questionResults && participant.questionResults.length > 0 ? `
                    <h4 class="font-medium text-gray-700 mb-3">Question Breakdown</h4>
                    <div class="space-y-3">
                        ${participant.questionResults.map((q, i) => `
                            <div class="flex justify-between items-center ${q.correct ? 'bg-green-50' : 'bg-red-50'} p-3 rounded-lg">
                                <div>
                                    <span class="${q.correct ? 'text-green-700' : 'text-red-700'} font-medium">Q${i + 1}:</span>
                                    <span class="text-gray-700 ml-2">${q.question || 'Question ' + (i + 1)}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="${q.correct ? 'text-green-700' : 'text-red-700'} mr-3">${q.timeSpent || 'N/A'}</span>
                                    <span class="${q.correct ? 'text-green-700' : 'text-red-700'} font-medium">${q.correct ? '+1' : '+0'}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    ` : '<p>No question data available</p>'}
                `;
                
                dom.participantModal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching participant details:', error);
                dom.modalContent.innerHTML = `<p class="text-red-500">Error loading participant details</p>`;
                dom.participantModal.classList.remove('hidden');
            });
    };

    // Initialize
    initLeaderboard();
});