package com.nusatim.partner.features.projects.ui.board

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse
import com.nusatim.partner.features.projects.domain.usecase.*
import com.nusatim.partner.features.projects.ui.board.state.ProjectsIntent
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.UnconfinedTestDispatcher
import kotlinx.coroutines.test.resetMain
import kotlinx.coroutines.test.runTest
import kotlinx.coroutines.test.setMain
import org.junit.After
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class ProjectsViewModelTest {

    private val getProjectsUseCase: GetProjectsUseCase = mockk()
    private val getProjectDetailUseCase: GetProjectDetailUseCase = mockk()
    private val claimProjectUseCase: ClaimProjectUseCase = mockk()
    private val cancelClaimProjectUseCase: CancelClaimProjectUseCase = mockk()

    private lateinit var viewModel: ProjectsViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)
        
        // Mock initial LoadProjects in init
        coEvery { getProjectsUseCase(any(), any()) } returns flowOf(ResultState.Loading)
        
        viewModel = ProjectsViewModel(
            getProjectsUseCase,
            getProjectDetailUseCase,
            claimProjectUseCase,
            cancelClaimProjectUseCase
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load projects successful, state should have data`() = runTest {
        val response = mockk<PagedBaseResponse<ProjectResponse>>()
        coEvery { getProjectsUseCase(any(), any()) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(response)
        )

        viewModel.processIntent(ProjectsIntent.LoadProjects(1))

        assertEquals(response, viewModel.state.value.projectsResponse)
        assertEquals(false, viewModel.state.value.isLoading)
    }

    @Test
    fun `when claim project successful, projects should be refreshed`() = runTest {
        coEvery { claimProjectUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(mockk())
        )
        val response = mockk<PagedBaseResponse<ProjectResponse>>()
        coEvery { getProjectsUseCase(any(), any()) } returns flowOf(ResultState.Success(response))

        viewModel.processIntent(ProjectsIntent.ClaimProject(1))

        coEvery { getProjectsUseCase(any(), any()) }
        assertEquals(response, viewModel.state.value.projectsResponse)
    }

    @Test
    fun `when cancel claim successful, projects should be refreshed`() = runTest {
        coEvery { cancelClaimProjectUseCase(1) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(mockk())
        )
        val response = mockk<PagedBaseResponse<ProjectResponse>>()
        coEvery { getProjectsUseCase(any(), any()) } returns flowOf(ResultState.Success(response))

        viewModel.processIntent(ProjectsIntent.CancelClaimProject(1))

        coEvery { getProjectsUseCase(any(), any()) }
        assertEquals(response, viewModel.state.value.projectsResponse)
    }
}
