<script>
function documentAnalysisData(initialFileId = 1) {
    return {
        // 상태 관리
        isLoading: false,
        showJsonManager: false,
        fileId: initialFileId,
        currentJsonVersion: 'v1.0',
        documentVersion: 'v1.0',
        displayedSections: 30,

        // 데이터
        documentData: {
            file: null,
            assets: []
        },

        // 편집 상태
        editingContent: {},
        editingStates: {},

        // 저장 관련
        saveFileName: '',
        savedJsonFiles: [],

        // 파일 목록
        fileNames: {
            1: 'Document 1.pdf',
            2: 'Document 2.pdf',
            3: 'Document 3.pdf',
            4: 'Document 4.pdf',
            5: 'Document 5.pdf',
            6: 'Document 6.pdf',
            7: 'Document 7.pdf'
        },

        // JSON 버전 목록
        availableJsonVersions: [
            { id: 'v1.0', name: 'v1.0 - AI 기술 동향 보고서 기본 분석' },
            { id: 'v2.0', name: 'v2.0 - AI 기술 동향 보고서 확장 분석' },
            { id: 'v3.0', name: 'v3.0 - 스마트 시티 플랫폼 제안서' }
        ],

        // 초기화
        init() {
            this.loadSavedJsonFiles();
            this.loadDocumentData();
        },

        // 문서 데이터 로드
        loadDocumentData() {
            this.isLoading = true;

            // 샘플 데이터
            setTimeout(() => {
                this.documentData = {
                    file: {
                        id: this.fileId,
                        original_name: this.fileNames[this.fileId]
                    },
                    assets: [
                        {
                            id: 1,
                            section_title: 'AI 기술 개요',
                            asset_type: 'introduction',
                            asset_type_name: '서론/개요',
                            asset_type_icon: '🎯',
                            content: '2025년 인공지능 기술은 생성형 AI의 급속한 발전으로 전 산업에 혁신을 가져오고 있습니다. ChatGPT, Claude, Gemini 등 대형 언어 모델의 등장으로 자연어 처리, 코드 생성, 창작 분야에서 인간 수준의 성능을 보여주고 있습니다.\n\n특히 멀티모달 AI 기술의 발전으로 텍스트, 이미지, 음성, 비디오를 통합적으로 처리할 수 있게 되었으며, 이는 기존 비즈니스 프로세스의 근본적인 변화를 이끌고 있습니다.',
                            summary: {
                                ai_summary: 'AI 기술이 2025년 생성형 AI 중심으로 급속 발전하며 전 산업에 혁신을 가져오고 있다는 개요입니다. 대형 언어 모델들이 인간 수준의 성능을 보여주며, 멀티모달 AI로 발전하고 있습니다.',
                                helpful_content: '우리 회사도 생성형 AI를 활용한 업무 자동화, 고객 서비스 개선, 콘텐츠 제작 효율화를 즉시 도입할 수 있습니다. 특히 문서 작성, 번역, 요약 업무에서 즉각적인 효과를 볼 수 있습니다.',
                                status_icon: '✅',
                                versions: [
                                    {
                                        id: 1,
                                        version_number: 1,
                                        version_display_name: 'v1 (AI 생성)',
                                        is_current: true
                                    }
                                ]
                            }
                        },
                        {
                            id: 2,
                            section_title: 'AI 시장 분석',
                            asset_type: 'analysis',
                            asset_type_name: '분석',
                            asset_type_icon: '📊',
                            content: '2025년 글로벌 AI 시장 규모는 1,847억 달러로, 전년 대비 37.3% 성장했습니다. 주요 성장 동력은 생성형 AI(45%), 자율주행(28%), 의료 AI(15%), 산업 자동화(12%) 순입니다.\n\n생성형 AI 분야에서는 OpenAI, Anthropic, Google이 선두를 달리고 있으며, 한국 기업들도 네이버 클로바X, 카카오브레인 등을 통해 경쟁력을 확보하고 있습니다.',
                            summary: {
                                ai_summary: 'AI 시장이 37.3% 성장하며 생성형 AI가 가장 큰 성장 동력(45%)으로 작용하고 있습니다. 글로벌 기업들과 한국 기업들의 경쟁 구도를 분석했습니다.',
                                helpful_content: '생성형 AI 시장 진입이 가장 유망합니다. 경쟁사 대비 2-3년의 기술 격차가 있어 빠른 투자 결정과 전문 인재 확보가 필요합니다. 네이버, 카카오와의 파트너십도 고려해볼 만합니다.',
                                status_icon: '✅',
                                versions: [
                                    {
                                        id: 1,
                                        version_number: 1,
                                        version_display_name: 'v1 (AI 생성)',
                                        is_current: false
                                    },
                                    {
                                        id: 2,
                                        version_number: 2,
                                        version_display_name: 'v2 (사용자 편집)',
                                        is_current: true
                                    }
                                ]
                            }
                        },
                        {
                            id: 3,
                            section_title: '전략적 제안',
                            asset_type: 'recommendation',
                            asset_type_name: '제안/권고',
                            asset_type_icon: '💡',
                            content: 'AI 기술 도입을 위한 3단계 로드맵을 제안합니다:\n\n1단계 (0-6개월): 기존 업무 프로세스 AI 적용\n- 문서 자동화, 번역, 요약\n- 고객 문의 챗봇 구축\n- 데이터 분석 자동화\n\n2단계 (6-18개월): 고객 대면 서비스 AI 고도화\n- 개인화 추천 시스템\n- 음성/영상 기반 서비스\n- 예측 분석 서비스\n\n3단계 (18개월 이후): 신사업 모델 개발\n- AI 기반 새로운 제품/서비스\n- 플랫폼 비즈니스 모델\n- 글로벌 시장 진출',
                            summary: {
                                ai_summary: '단계적 AI 도입 전략으로 업무 효율화부터 신사업 개발까지 체계적 접근을 제안합니다. 3단계로 나누어 점진적으로 AI 역량을 확장하는 방안입니다.',
                                helpful_content: '1단계부터 즉시 시작 가능합니다. 문서 자동화, 고객 문의 챗봇부터 시작해 점진적으로 확장하는 것이 현실적입니다. 각 단계별로 ROI 측정과 성과 평가를 통해 다음 단계로 진행하면 됩니다.',
                                status_icon: '✅',
                                versions: [
                                    {
                                        id: 1,
                                        version_number: 1,
                                        version_display_name: 'v1 (AI 생성)',
                                        is_current: true
                                    }
                                ]
                            }
                        }
                    ]
                };

                this.isLoading = false;
            }, 500);
        },

        // 파일 변경
        changeFile(newFileId) {
            this.fileId = parseInt(newFileId);
            this.loadDocumentData();
        },

        // JSON 버전 로드
        loadJsonVersion(versionId) {
            this.currentJsonVersion = versionId;
            this.loadDocumentData();
        },

        // 에셋 타입별 색상
        getAssetBorderColor(assetType) {
            const colors = {
                'introduction': 'border-blue-400',
                'analysis': 'border-green-400',
                'recommendation': 'border-gray-400',
                'conclusion': 'border-purple-400',
                'data': 'border-yellow-400'
            };
            return colors[assetType] || 'border-gray-400';
        },

        // 편집 모드 체크
        isEditing(index, field) {
            return this.editingStates[index]?.[field] || false;
        },

        // 편집 모드 토글
        toggleEditMode(index, field) {
            if (!this.editingStates[index]) {
                this.editingStates[index] = {};
            }

            if (!this.editingContent[index]) {
                this.editingContent[index] = {};
            }

            if (this.editingStates[index][field]) {
                // 편집 취소
                this.editingStates[index][field] = false;
            } else {
                // 편집 시작
                this.editingStates[index][field] = true;
                this.editingContent[index][field] = this.documentData.assets[index].summary?.[field] || '';
            }
        },

        // 편집 저장
        saveEdit(index, field) {
            const newContent = this.editingContent[index][field];

            if (!this.documentData.assets[index].summary) {
                this.documentData.assets[index].summary = {};
            }

            if (!this.documentData.assets[index].summary.versions) {
                this.documentData.assets[index].summary.versions = [];
            }

            // 새 버전 생성
            const newVersionNumber = this.documentData.assets[index].summary.versions.length + 1;

            // 기존 버전 is_current를 false로
            this.documentData.assets[index].summary.versions.forEach(v => v.is_current = false);

            // 새 버전 추가
            this.documentData.assets[index].summary.versions.push({
                id: newVersionNumber,
                version_number: newVersionNumber,
                version_display_name: `v${newVersionNumber} (사용자 편집)`,
                is_current: true,
                created_at: new Date().toISOString()
            });

            // 컨텐츠 업데이트
            this.documentData.assets[index].summary[field] = newContent;

            // 편집 모드 종료
            this.editingStates[index][field] = false;

            alert('저장되었습니다. 새 버전이 생성되었습니다.');
        },

        // 편집 취소
        cancelEdit(index, field) {
            this.editingStates[index][field] = false;
        },

        // 섹션 버전 전환
        switchSectionVersion(index, versionNumber) {
            const asset = this.documentData.assets[index];
            if (!asset.summary?.versions) return;

            // 모든 버전의 is_current를 false로
            asset.summary.versions.forEach(v => v.is_current = false);

            // 선택한 버전을 current로
            const selectedVersion = asset.summary.versions.find(v => v.version_number == versionNumber);
            if (selectedVersion) {
                selectedVersion.is_current = true;
                // 여기서 실제로는 버전별 컨텐츠를 로드해야 하지만, 샘플이므로 생략
                alert(`버전 ${versionNumber}으로 전환되었습니다.`);
            }
        },

        // 로컬 스토리지 관련
        loadSavedJsonFiles() {
            const saved = localStorage.getItem('rfx_saved_json_files');
            this.savedJsonFiles = saved ? JSON.parse(saved) : [];
        },

        saveToLocalStorage() {
            if (!this.saveFileName) {
                alert('파일명을 입력하세요.');
                return;
            }

            const newFile = {
                id: Date.now(),
                fileName: this.saveFileName,
                version: this.currentJsonVersion,
                documentVersion: this.documentVersion,
                originalFileName: this.fileNames[this.fileId],
                sectionsCount: this.documentData.assets?.length || 0,
                createdAt: new Date().toISOString(),
                data: JSON.stringify(this.documentData)
            };

            this.savedJsonFiles.push(newFile);
            localStorage.setItem('rfx_saved_json_files', JSON.stringify(this.savedJsonFiles));

            this.saveFileName = '';
            alert('저장되었습니다.');
        },

        loadFromLocalStorage(fileId) {
            const file = this.savedJsonFiles.find(f => f.id === fileId);
            if (file) {
                this.documentData = JSON.parse(file.data);
                this.currentJsonVersion = file.version;
                this.documentVersion = file.documentVersion || 'v1.0';
                this.showJsonManager = false;
                alert('불러오기 완료!');
            }
        },

        deleteFromLocalStorage(fileId) {
            if (confirm('정말로 삭제하시겠습니까?')) {
                this.savedJsonFiles = this.savedJsonFiles.filter(f => f.id !== fileId);
                localStorage.setItem('rfx_saved_json_files', JSON.stringify(this.savedJsonFiles));
                alert('삭제되었습니다.');
            }
        },

        clearAllLocalStorage() {
            if (confirm('정말로 모든 저장된 데이터를 삭제하시겠습니까?')) {
                localStorage.removeItem('rfx_saved_json_files');
                this.savedJsonFiles = [];
                alert('전체 삭제 완료!');
            }
        },

        getTotalStorageSize() {
            const total = this.savedJsonFiles.reduce((sum, file) => {
                return sum + (file.data?.length || 0);
            }, 0);
            return Math.round(total / 1024);
        },

        getUniqueVersionsCount() {
            const versions = new Set(this.savedJsonFiles.map(f => f.version));
            return versions.size;
        },

        downloadCurrentJson() {
            const dataStr = JSON.stringify(this.documentData, null, 2);
            const blob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `rfx_analysis_${this.fileNames[this.fileId]}_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const data = JSON.parse(e.target.result);
                    this.documentData = data;
                    this.showJsonManager = false;
                    alert('JSON 파일 불러오기 완료!');
                } catch (error) {
                    alert('JSON 파일 파싱 오류: ' + error.message);
                }
            };
            reader.readAsText(file);
        },

        saveCurrentJson() {
            if (!this.saveFileName) {
                this.saveFileName = `분석_${this.fileNames[this.fileId]}_${new Date().toISOString().split('T')[0]}`;
            }
            this.showJsonManager = true;
        }
    };
}
</script>
