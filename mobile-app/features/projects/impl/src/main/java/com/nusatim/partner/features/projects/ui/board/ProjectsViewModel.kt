package com.nusatim.partner.features.projects.ui.board

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.projects.domain.usecase.*
import com.nusatim.partner.features.projects.ui.board.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ProjectsViewModel @Inject constructor(
    private val getProjectsUseCase: GetProjectsUseCase,
    private val getProjectDetailUseCase: GetProjectDetailUseCase,
    private val claimProjectUseCase: ClaimProjectUseCase,
    private val cancelClaimProjectUseCase: CancelClaimProjectUseCase
) : BaseViewModel<ProjectsState, ProjectsIntent, ProjectsEffect>(ProjectsState()) {

    init {
        processIntent(ProjectsIntent.LoadProjects())
    }

    override fun processIntent(intent: ProjectsIntent) {
        when (intent) {
            is ProjectsIntent.LoadProjects -> loadProjects(intent.page)
            is ProjectsIntent.LoadProjectDetail -> loadProjectDetail(intent.id)
            is ProjectsIntent.ClaimProject -> claimProject(intent.id)
            is ProjectsIntent.CancelClaimProject -> cancelClaimProject(intent.id)
        }
    }

    private fun loadProjects(page: Int) {
        viewModelScope.launch {
            getProjectsUseCase(page = page).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, projectsResponse = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { ProjectsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadProjectDetail(id: Int) {
        viewModelScope.launch {
            getProjectDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, selectedProject = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { ProjectsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun claimProject(id: Int) {
        viewModelScope.launch {
            claimProjectUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        loadProjects(1) // Refresh
                        sendEffect { ProjectsEffect.ShowSuccess("Berhasil diklaim, menunggu persetujuan admin") }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        val errorMessage = if (result.message.contains("404") || result.message.contains("409")) {
                            "Proyek ini sudah tidak tersedia, mungkin baru saja diklaim partner lain."
                        } else {
                            result.message
                        }
                        sendEffect { ProjectsEffect.ShowError(errorMessage) }
                        if (result.message.contains("404") || result.message.contains("409")) {
                            loadProjects(1) // Auto refresh
                        }
                    }
                }
            }
        }
    }

    private fun cancelClaimProject(id: Int) {
        viewModelScope.launch {
            cancelClaimProjectUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        loadProjects(1) // Refresh
                        sendEffect { ProjectsEffect.ShowSuccess("Klaim dibatalkan") }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        val errorMessage = if (result.message.contains("404") || result.message.contains("409")) {
                            "Proyek ini sudah tidak tersedia, mungkin baru saja diklaim partner lain."
                        } else {
                            result.message
                        }
                        sendEffect { ProjectsEffect.ShowError(errorMessage) }
                        if (result.message.contains("404") || result.message.contains("409")) {
                            loadProjects(1) // Auto refresh
                        }
                    }
                }
            }
        }
    }
}
