<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Allura&family=Handlee&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
    <style>
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-[#E5E5E5] min-h-screen flex flex-col">
    <div class="relative">
        <input type="checkbox" id="close-banner" class="hidden peer" />
        <div class="bg-[#E1B6FF] py-2 flex flex-row pl-4 relative peer-checked:hidden">
            <p class="text-lg font-semibold">Unlock unlimited participants and new features.</p>
            <button class="flex flex-row bg-[#EFDAFE] text-[#A435F0] border-2 border-[#A435F0] rounded-xl px-2 ml-2 hover:bg-purple-300 transition duration-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] text-[#A435F0] py-[2px]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.0049 22.0027C6.48204 22.0027 2.00488 17.5256 2.00488 12.0027C2.00488 6.4799 6.48204 2.00275 12.0049 2.00275C17.5277 2.00275 22.0049 6.4799 22.0049 12.0027C22.0049 17.5256 17.5277 22.0027 12.0049 22.0027ZM12.0049 20.0027C16.4232 20.0027 20.0049 16.421 20.0049 12.0027C20.0049 7.58447 16.4232 4.00275 12.0049 4.00275C7.5866 4.00275 4.00488 7.58447 4.00488 12.0027C4.00488 16.421 7.5866 20.0027 12.0049 20.0027ZM8.50488 14.0027H14.0049C14.281 14.0027 14.5049 13.7789 14.5049 13.5027C14.5049 13.2266 14.281 13.0027 14.0049 13.0027H10.0049C8.62417 13.0027 7.50488 11.8835 7.50488 10.5027C7.50488 9.12203 8.62417 8.00275 10.0049 8.00275H11.0049V6.00275H13.0049V8.00275H15.5049V10.0027H10.0049C9.72874 10.0027 9.50488 10.2266 9.50488 10.5027C9.50488 10.7789 9.72874 11.0027 10.0049 11.0027H14.0049C15.3856 11.0027 16.5049 12.122 16.5049 13.5027C16.5049 14.8835 15.3856 16.0027 14.0049 16.0027H13.0049V18.0027H11.0049V16.0027H8.50488V14.0027Z"></path>
                </svg>Upgrade?</button>
            <label for="close-banner" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-[#A435F0] text-xl leading-none hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px] text-[#A435F0] hover:text-purple-700" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM12 10.5858L14.8284 7.75736L16.2426 9.17157L13.4142 12L16.2426 14.8284L14.8284 16.2426L12 13.4142L9.17157 16.2426L7.75736 14.8284L10.5858 12L7.75736 9.17157L9.17157 7.75736L12 10.5858Z"></path>
                </svg>
            </label>
        </div>
    </div>

    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center gap-2">
            <a href="#" class="text-xl">&#8592;</a>
            <span class="text-lg font-medium">NameOfQuiz</span>
        </div>

        <div class="flex gap-4">
            <a href="#" class="underline text-lg">Create</a>
            <a href="#" class="text-lg">Results</a>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <img src="../assets/profilepic/demo.jpg" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                <button class="absolute -top-1 -right-1 w-5 h-5 bg-gray-200 text-black rounded-full text-xs flex items-center justify-center">+</button>
            </div>
            <button class="bg-gray-200 px-4 py-1 rounded-full text-lg">Share</button>
            <button class="bg-[#A435F0] text-white px-4 py-1 rounded-full text-lg hover:bg-purple-700">Present</button>
        </div>
    </div>

    <div class="flex flex-row bg-[#E5E5E5] h-full mt-8">
        <div class="flex flex-col w-[210px] pl-6">
            <button class="bg-[#A435F0] text-white h-[40px] w-[170px] flex justify-center items-center rounded-3xl hover:bg-purple-700">+ New Slide</button>
            <button class="relative flex w-[170px] h-[94px] bg-white items-center justify-center mt-8 rounded-lg border border-[#D0D0D0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 3C21.5523 3 22 3.44772 22 4V18C22 18.5523 21.5523 19 21 19H6.455L2 22.5V4C2 3.44772 2.44772 3 3 3H21ZM20 5H4V18.385L5.76333 17H20V5ZM13 7V15H11V7H13ZM17 9V15H15V9H17ZM9 11V15H7V11H9Z"></path>
                </svg>

                <button class="absolute bottom-2 right-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 12a2 2 0 114 0 2 2 0 01-4 0zm4 0a2 2 0 104 0 2 2 0 00-4 0zm4 0a2 2 0 104 0 2 2 0 00-4 0z" />
                    </svg>
                </button>
            </button>

            <button class="relative flex w-[170px] h-[94px] bg-white items-center justify-center mt-8 rounded-lg border border-[#D0D0D0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[24px] w-[24px]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 3C21.5523 3 22 3.44772 22 4V18C22 18.5523 21.5523 19 21 19H6.455L2 22.5V4C2 3.44772 2.44772 3 3 3H21ZM20 5H4V18.385L5.76333 17H20V5ZM13 7V15H11V7H13ZM17 9V15H15V9H17ZM9 11V15H7V11H9Z"></path>
                </svg>

                <button class="absolute bottom-2 right-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 12a2 2 0 114 0 2 2 0 01-4 0zm4 0a2 2 0 104 0 2 2 0 00-4 0zm4 0a2 2 0 104 0 2 2 0 00-4 0z" />
                    </svg>
                </button>
            </button>

        </div>
        <div class="bg-white border-4 border-purple-300 ml-2 rounded-lg p-10 w-full max-w-4xl h-[500px] shadow-lg">
            <h1 class="text-3xl font-semibold text-black mb-8">Type your question here...</h1>

            <h2 class="text-xl font-medium text-black mb-4">Options</h2>

            <div class="grid grid-cols-2 gap-6 pt-8">
                <button class="bg-red-500 text-black font-semibold py-4 rounded-lg hover:bg-red-600 transition">Option1</button>
                <button class="bg-yellow-400 text-black font-semibold py-4 rounded-lg hover:bg-yellow-500 transition">Option2</button>
                <button class="bg-sky-400 text-black font-semibold py-4 rounded-lg hover:bg-sky-500 transition">Option3</button>
                <button class="bg-green-500 text-black font-semibold py-4 rounded-lg hover:bg-green-600 transition">Option4</button>
            </div>
        </div>
        <div class="w-[300px] ml-8 bg-white p-5 rounded-xl shadow-md relative font-sans text-sm">
            <button class="absolute top-3 right-3 text-2xl text-gray-500">&times;</button>

            <h2 class="text-lg font-semibold mb-4">Slide</h2>

            <label class="text-xs font-medium text-gray-700 mb-1 block">Question Type</label>
            <select class="w-full bg-gray-100 p-2 rounded-md mb-4 text-sm text-gray-800 focus:outline-none">
                <option>Multiple Choice</option>
                <option>Fill in the blank</option>
                <option>Survey Quiz</option>
            </select>

            <label class="text-xs font-medium text-gray-700 block mb-1">Image</label>
            <p class="text-xs text-gray-400 mb-2">We support .jpg, .png, .jpeg and .png</p>
            <div class="border border-dashed border-gray-300 rounded-md flex items-center justify-center h-20 mb-5 cursor-pointer bg-gray-50 text-gray-500 text-sm">
                <span>Drag and Drop or <span class="underline">Click here to add image</span></span>
            </div>

            <hr class="border-t border-purple-300 mb-4">

            <label class="text-xs font-medium text-gray-700 block mb-1">Text</label>
            <div class="flex items-center gap-2 mb-2">
                <label class="text-xs">Choose Text color</label>
                <input type="color" class="w-5 h-5 rounded-full border">
            </div>
            <div class="mb-4">
                <label class="text-xs block mb-1">Choose Font</label>
                <select class="border rounded-md px-2 py-1 w-full">
                    <option>OutFit</option>
                </select>
            </div>

            <label class="text-xs font-medium text-gray-700 block mb-1">Background</label>
            <div class="flex items-center gap-2 mb-2">
                <label class="text-xs">Choose Background Color</label>
                <input type="color" class="w-5 h-5 rounded-full border">
            </div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs">Choose Background Image</label>
                <button class="bg-gray-100 px-2 py-1 rounded-md text-sm">Add +</button>
            </div>

            <p class="text-xs underline text-gray-500 cursor-pointer">Reset default background</p>
        </div>
        <div class="flex flex-col h-1/2 gap-4 bg-white p-3 rounded-xl shadow w-[100px] ml-10">
            <button class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5l3 3L12 15H9v-3L18.5 2.5z" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Edit</span>
            </button>

            <button class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2h10z" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Delete</span>
            </button>

            <button class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Theme</span>
            </button>

            <button class="flex flex-col items-center justify-center bg-gray-100 border border-purple-300 rounded-lg py-2 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 3h9l5 5v13a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1zm9 0v5h5" />
                </svg>
                <span class="text-xs mt-1 text-gray-700">Templete</span>
            </button>
        </div>


    </div>
</body>

</html>