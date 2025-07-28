<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="서비스 이용약관" />

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">서비스 이용약관</h1>
                
                <div class="text-sm text-gray-700 leading-relaxed space-y-4">
                    <p>
                        본 약관은 귀하가 당사의 웹호스팅 SaaS 서비스를 이용함에 있어 필요한 조건, 권리 및 의무를 규정합니다.
                    </p>

                    <p>
                        <strong>제1조 (목적)</strong><br>
                        본 약관은 사용자가 본 서비스에 가입하고, cPanel를 기반으로 한 웹사이트를 생성하고 운영하는 과정에서 발생할 수 있는 기본적인 이용 조건을 규정합니다.
                    </p>

                    <p>
                        <strong>제2조 (서비스 내용)</strong><br>
                        당사는 사용자의 신청에 따라 cPanel 기반 웹사이트를 자동으로 생성하며, WHM 기반 리셀러 서버를 통해 웹호스팅 환경을 제공합니다. 서비스 구성은 선택한 요금제에 따라 상이할 수 있습니다.
                    </p>

                    <p>
                        <strong>제3조 (회원가입 및 계정)</strong><br>
                        사용자는 유효한 이메일, 비밀번호, 연락처 등의 정보를 제공하고, 정해진 절차를 통해 회원가입을 완료해야 합니다. 회원 정보는 사용자 본인의 책임 하에 관리되어야 하며, 타인에게 공유 또는 양도할 수 없습니다.
                    </p>

                    <p>
                        <strong>제4조 (서비스 이용 요금)</strong><br>
                        서비스는 유료이며, 요금제에 따라 월 단위 또는 정기 결제 방식으로 운영됩니다. 결제 금액 및 조건은 서비스 페이지에 명시된 내용을 따릅니다.
                    </p>

                    <p>
                        <strong>제5조 (계정 정지 및 해지)</strong><br>
                        다음과 같은 경우 당사는 사전 통보 없이 계정 정지 또는 해지를 할 수 있습니다:<br>
                        - 이용약관을 위반하거나 불법적인 콘텐츠를 운영한 경우<br>
                        - cPanel 외 목적의 무단 사용, 리소스 과다 사용 등<br>
                        - 저작권 침해 목적 사용<br>
                        - 기타 서비스 안정성에 심각한 영향을 줄 경우
                    </p>

                    <p>
                        <strong>제6조 (데이터 보관 및 삭제)</strong><br>
                        서비스 해지 후 7일간 데이터가 보관되며, 이후 자동 삭제됩니다. 해지 전 백업은 사용자 책임입니다.
                    </p>

                    <p>
                        <strong>제7조 (면책 조항)</strong><br>
                        당사는 아래 사유로 인한 피해에 대해 책임지지 않습니다:<br>
                        - 사용자의 과실로 인한 정보 유출 또는 손해<br>
                        - IDC/서버 장애, 천재지변, 통신망 문제 등 외부 요인<br>
                        - 사용자의 콘텐츠 관리 부주의
                    </p>

                    <p>
                        <strong>제8조 (약관 변경)</strong><br>
                        당사는 서비스 개선을 위해 약관을 변경할 수 있으며, 변경 시 웹사이트에 사전 공지합니다.
                    </p>

                    <p class="text-xs text-gray-500">
                        본 약관은 2025년 6월 기준으로 작성되었으며, 최신 버전은 서비스 페이지를 통해 확인할 수 있습니다.
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <a href="/login" class="text-purple-600 hover:text-purple-800 font-medium">
                        ← 로그인으로 돌아가기
                    </a>
                </div>
            </div>
        </div>
    </div>
</template> 