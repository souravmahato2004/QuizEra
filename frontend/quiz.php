<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
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

    <div class="bg-white border-4 border-purple-300 rounded-lg p-10 w-full max-w-4xl shadow-lg">
        <h1 class="text-3xl font-semibold text-black mb-8">Type your question here...</h1>

        <h2 class="text-xl font-medium text-black mb-4">Options</h2>

        <div class="grid grid-cols-2 gap-6">
            <button class="bg-red-500 text-black font-semibold py-4 rounded-lg hover:bg-red-600 transition">Option1</button>
            <button class="bg-yellow-400 text-black font-semibold py-4 rounded-lg hover:bg-yellow-500 transition">Option2</button>
            <button class="bg-sky-400 text-black font-semibold py-4 rounded-lg hover:bg-sky-500 transition">Option3</button>
            <button class="bg-green-500 text-black font-semibold py-4 rounded-lg hover:bg-green-600 transition">Option4</button>
        </div>
    </div>
</body>

</html>