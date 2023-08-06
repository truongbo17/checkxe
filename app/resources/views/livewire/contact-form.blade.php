<div class="relative bg-white">
    <div class="absolute inset-0">
        <div class="absolute inset-y-0 left-0 w-1/2 bg-gray-50"></div>
    </div>
    <div class="relative max-w-7xl mx-auto md:grid md:grid-cols-2 lg:grid lg:grid-cols-5">
        <div class="bg-gray-50 py-16 px-4 sm:px-6 lg:col-span-2 lg:px-8 lg:py-24 xl:pr-12">
            <div class="max-w-lg mx-auto">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
                    Liên hệ / Góp ý
                </h2>
                <div
                    class="inline-flex mt-3 text-orange-400 items-center justify-center text-sm max-w-2xl mx-auto text-gray-500 sm:mt-4">
                    <svg fill="none" class="flex-shrink-0 h-4 w-4" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                    </svg>
                    <p class="pl-2">
                        Không phải không tìm thấy dữ liệu tai nạn trên web là 100% xe đó không bị gì, website chỉ giúp
                        người
                        mua kiểm tra nhanh sơ qua 1 phần, thay vì chờ gara kiểm tra, nếu xe đó không có trong dữ liệu
                        web
                        thì nên đem xe ra gara kiểm tra thêm cho đảm bảo 100% ạ, còn xe đó có hình ảnh tai nạn trên web
                        rồi
                        thì người mua sẽ xem xét mức độ nặng nhẹ của xe để quyết định có nên mua hay không.
                    </p>
                </div>
                <div
                    class="inline-flex mt-3 text-orange-400 items-center justify-center text-sm max-w-2xl mx-auto text-gray-500 sm:mt-4">
                    <svg fill="none" class="flex-shrink-0 h-4 w-4" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                    </svg>
                    <p class="pl-2">
                        Dữ liệu trên hệ thống chỉ mang tính chất tham khảo, chúng tôi sẽ không chịu trách nhiệm pháp lý
                        nếu
                        bạn sử dụng sai mục đích.
                    </p>
                </div>
                <div class="flex items-center mt-6 text-base text-gray-500">
                    <span>Liên hệ trực tiếp</span>
                    <a href="https://t.me/truong_bo" target="_blank" class="font-medium text-gray-700 pl-2 text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-telegram" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white py-16 px-4 sm:px-6 lg:col-span-3 lg:py-24 lg:px-8 xl:pl-12">
            <div class="max-w-lg mx-auto lg:max-w-none">
                @if (session()->has('success_create_contact'))
                    <p class="text-green-600 pb-3">{{ session('success_create_contact') }}</p>
                @endif
                <form wire:submit.prevent="submit" class="grid grid-cols-1 gap-y-6">
                    <div>
                        <label for="full-name" class="sr-only">Họ tên</label>
                        <input type="text" wire:model="name" id="full-name" autocomplete="name"
                               class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
                               placeholder="Họ tên">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" wire:model="email" type="email" autocomplete="email"
                               class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
                               placeholder="Email">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="message" class="sr-only">Nội dung</label>
                        <textarea id="message" wire:model="content" rows="4"
                                  class="block w-full shadow-sm py-3 px-4 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 border border-gray-300 rounded-md"
                                  placeholder="Nội dung"></textarea>
                        @error('content') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <button type="submit"
                                class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Liên hệ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
