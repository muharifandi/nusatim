#!/bin/bash

# Partner Feature Generator (Standardized for XML/View and Navigation)
# Usage: ./generate_feature.sh <feature_name>

FEATURE_NAME=$1

if [ -z "$FEATURE_NAME" ]; then
    echo "Error: Nama fitur harus diisi. Contoh: ./generate_feature.sh login"
    exit 1
fi

# Konfigurasi Header
AUTHOR="Muh. Arifandi"
EMAIL="arif76440@gmail.com"
PROJECT="My Application"
CURRENT_DATE=$(date +"%d/%m/%Y")

# Nama paket dasar
BASE_PACKAGE="com.nusatim.partner"
# Konversi titik ke slash untuk path direktori
PACKAGE_PATH=${BASE_PACKAGE//.//}/features/${FEATURE_NAME}

FEATURE_PACKAGE="${BASE_PACKAGE}.features.${FEATURE_NAME}"
FEATURE_DIR="features/${FEATURE_NAME}"
MODULE_NAME_API="features:${FEATURE_NAME}:api"
MODULE_NAME_IMPL="features:${FEATURE_NAME}:impl"

echo "🚀 Memulai pembuatan fitur: ${FEATURE_NAME} (XML/View version)..."

# 1. Buat struktur folder
mkdir -p "${FEATURE_DIR}/api/src/main/java/${PACKAGE_PATH}/api"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/data/network"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/data/repository"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/data/mapper"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/domain/model"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/domain/usecase"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/domain/repository"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/state"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/fragment"
mkdir -p "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/di"
mkdir -p "${FEATURE_DIR}/impl/src/main/res/layout"

# Fungsi untuk membuat Header
create_header() {
    local file_name=$1
    local module_name=$2
    cat <<EOF
/**
 * Created by ${AUTHOR} on ${CURRENT_DATE}
 * Email : ${EMAIL}
 * Project : ${PROJECT}
 * Module : ${module_name}
 * File : ${file_name}
 */
EOF
}

# 2. Buat build.gradle.kts untuk API
cat <<EOF > "${FEATURE_DIR}/api/build.gradle.kts"
plugins {
    id("myapp.kotlin.library")
    alias(libs.plugins.kotlin.serialization)
}

dependencies {
    implementation(libs.kotlinx.serialization.json)
}
EOF

# 3. Buat build.gradle.kts untuk IMPL
cat <<EOF > "${FEATURE_DIR}/impl/build.gradle.kts"
plugins {
    id("myapp.android.feature")
    id("myapp.android.hilt")
}

android {
    namespace = "${FEATURE_PACKAGE}"
}

dependencies {
    implementation(project(":features:${FEATURE_NAME}:api"))
    implementation(project(":core:architecture"))
    implementation(project(":core:ui"))
    implementation(project(":core:network"))
    implementation(project(":core:common"))
    implementation(project(":core:model"))
    implementation(project(":navigation"))
}
EOF

# 4. Buat file MVI State
CLASS_NAME_PREFIX="$(tr '[:lower:]' '[:upper:]' <<< ${FEATURE_NAME:0:1})${FEATURE_NAME:1}"

# State
{
    create_header "${CLASS_NAME_PREFIX}State.kt" "${MODULE_NAME_IMPL}"
    cat <<EOF
package ${FEATURE_PACKAGE}.ui.state

import com.nusatim.partner.core.architecture.mvi.UiState

data class ${CLASS_NAME_PREFIX}State(
    val isLoading: Boolean = false,
    val data: String? = null
) : UiState
EOF
} > "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/state/${CLASS_NAME_PREFIX}State.kt"

# Intent
{
    create_header "${CLASS_NAME_PREFIX}Intent.kt" "${MODULE_NAME_IMPL}"
    cat <<EOF
package ${FEATURE_PACKAGE}.ui.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface ${CLASS_NAME_PREFIX}Intent : UiIntent {
    data object LoadInitialData : ${CLASS_NAME_PREFIX}Intent
}
EOF
} > "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/state/${CLASS_NAME_PREFIX}Intent.kt"

# Effect
{
    create_header "${CLASS_NAME_PREFIX}Effect.kt" "${MODULE_NAME_IMPL}"
    cat <<EOF
package ${FEATURE_PACKAGE}.ui.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface ${CLASS_NAME_PREFIX}Effect : UiEffect {
    data class ShowError(val message: String) : ${CLASS_NAME_PREFIX}Effect
}
EOF
} > "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/state/${CLASS_NAME_PREFIX}Effect.kt"

# 5. Buat Layout XML
LOWER_FEATURE=$(echo "$FEATURE_NAME" | tr '[:upper:]' '[:lower:]')
LAYOUT_NAME="fragment_${LOWER_FEATURE}"
cat <<EOF > "${FEATURE_DIR}/impl/src/main/res/layout/${LAYOUT_NAME}.xml"
<?xml version="1.0" encoding="utf-8"?>
<androidx.constraintlayout.widget.ConstraintLayout xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    android:layout_width="match_parent"
    android:layout_height="match_parent">

    <ProgressBar
        android:id="@+id/progress_bar"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:visibility="gone"
        app:layout_constraintBottom_toBottomOf="parent"
        app:layout_constraintEnd_toEndOf="parent"
        app:layout_constraintStart_toStartOf="parent"
        app:layout_constraintTop_toTopOf="parent" />

    <TextView
        android:id="@+id/tv_welcome"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Welcome to ${CLASS_NAME_PREFIX} Feature"
        app:layout_constraintBottom_toBottomOf="parent"
        app:layout_constraintEnd_toEndOf="parent"
        app:layout_constraintStart_toStartOf="parent"
        app:layout_constraintTop_toTopOf="parent" />

</androidx.constraintlayout.widget.ConstraintLayout>
EOF

# 6. Buat ViewModel
{
    create_header "${CLASS_NAME_PREFIX}ViewModel.kt" "${MODULE_NAME_IMPL}"
    cat <<EOF
package ${FEATURE_PACKAGE}.ui

import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import ${FEATURE_PACKAGE}.ui.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import javax.inject.Inject

@HiltViewModel
class ${CLASS_NAME_PREFIX}ViewModel @Inject constructor() :
    BaseViewModel<${CLASS_NAME_PREFIX}State, ${CLASS_NAME_PREFIX}Intent, ${CLASS_NAME_PREFIX}Effect>(${CLASS_NAME_PREFIX}State()) {

    override fun processIntent(intent: ${CLASS_NAME_PREFIX}Intent) {
        when (intent) {
            is ${CLASS_NAME_PREFIX}Intent.LoadInitialData -> { /* Logic */ }
        }
    }
}
EOF
} > "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/${CLASS_NAME_PREFIX}ViewModel.kt"

# 7. Buat Fragment
{
    create_header "${CLASS_NAME_PREFIX}Fragment.kt" "${MODULE_NAME_IMPL}"
    cat <<EOF
package ${FEATURE_PACKAGE}.ui.fragment

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.view.isVisible
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import ${FEATURE_PACKAGE}.databinding.Fragment${CLASS_NAME_PREFIX}Binding
import ${FEATURE_PACKAGE}.ui.${CLASS_NAME_PREFIX}ViewModel
import ${FEATURE_PACKAGE}.ui.state.${CLASS_NAME_PREFIX}State
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ${CLASS_NAME_PREFIX}Fragment : Fragment() {

    private val viewModel: ${CLASS_NAME_PREFIX}ViewModel by viewModels()
    private var _binding: Fragment${CLASS_NAME_PREFIX}Binding? = null
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = Fragment${CLASS_NAME_PREFIX}Binding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        observeState()
    }

    private fun observeState() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    render(state)
                }
            }
        }
    }

    private fun render(state: ${CLASS_NAME_PREFIX}State) {
        binding.progressBar.isVisible = state.isLoading
        state.data?.let {
            binding.tvWelcome.text = it
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
EOF
} > "${FEATURE_DIR}/impl/src/main/java/${PACKAGE_PATH}/ui/fragment/${CLASS_NAME_PREFIX}Fragment.kt"

# 8. Daftarkan di settings.gradle.kts
if ! grep -q ":features:${FEATURE_NAME}:api" settings.gradle.kts; then
    echo "include(\":features:${FEATURE_NAME}:api\")" >> settings.gradle.kts
fi
if ! grep -q ":features:${FEATURE_NAME}:impl" settings.gradle.kts; then
    echo "include(\":features:${FEATURE_NAME}:impl\")" >> settings.gradle.kts
fi

echo "✅ Fitur ${FEATURE_NAME} berhasil dibuat dengan standar Fragment & XML!"
echo "⚠️  Langkah selanjutnya:"
echo "1. Lakukan 'Gradle Sync'."
echo "2. Daftarkan Fragment di nav_graph (biasanya di app/src/main/res/navigation/)."
echo "3. Update ${CLASS_NAME_PREFIX}Fragment untuk logic UI spesifik."
