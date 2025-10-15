<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Keycloak SSO 테스트 페이지</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @if($isAuthenticated)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-green-600">✓ Keycloak 인증 성공</h2>

                    @if($user)
                        <div class="bg-gray-50 p-4 rounded mb-4">
                            <h3 class="font-semibold mb-2">사용자 정보:</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="font-medium text-gray-700">이름:</dt>
                                    <dd class="text-gray-900">{{ $user->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">이메일:</dt>
                                    <dd class="text-gray-900">{{ $user->email ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">ID:</dt>
                                    <dd class="text-gray-900">{{ $user->id ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif

                    <button
                        wire:click="logout"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    >
                        로그아웃
                    </button>
                </div>
            @else
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-600">Keycloak 인증 필요</h2>
                    <p class="mb-4 text-gray-700">
                        Keycloak SSO를 통해 로그인하려면 아래 버튼을 클릭하세요.
                    </p>

                    <button
                        wire:click="redirectToKeycloak"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    >
                        Keycloak으로 로그인
                    </button>
                </div>
            @endif

            <div class="border-t pt-4 mt-6">
                <h3 class="font-semibold mb-2">설정 상태:</h3>
                <div class="bg-gray-50 p-4 rounded text-sm">
                    <div class="mb-2">
                        <span class="font-medium">Keycloak URL:</span>
                        <span class="text-gray-600">{{ env('KEYCLOAK_BASE_URL') ?: '미설정' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium">Realm:</span>
                        <span class="text-gray-600">{{ env('KEYCLOAK_REALM') ?: '미설정' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium">Client ID:</span>
                        <span class="text-gray-600">{{ env('KEYCLOAK_CLIENT_ID') ?: '미설정' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Redirect URI:</span>
                        <span class="text-gray-600">{{ env('KEYCLOAK_REDIRECT_URI') ?: '미설정' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="/" class="text-blue-500 hover:text-blue-700 underline">
                대시보드로 돌아가기
            </a>
        </div>
    </div>
</div>
