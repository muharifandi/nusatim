package com.nusatim.partner.features.projects.ui.board

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import com.google.android.material.tabs.TabLayout
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.projects.databinding.FragmentProjectsBinding
import com.nusatim.partner.features.projects.ui.board.state.ProjectsEffect
import com.nusatim.partner.features.projects.ui.board.state.ProjectsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ProjectsFragment : BaseFragment<FragmentProjectsBinding>() {

    private val viewModel: ProjectsViewModel by viewModels()
    private val adapter by lazy {
        ProjectsAdapter(
            onItemClick = { project ->
                findNavController().navigate("partner://projects/detail?id=${project.id}".toUri())
            },
            onClaimClick = { project ->
                showClaimConfirmation(project)
            }
        )
    }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.projects.R.string.projects_title)
            navigationIcon = null
        }
        binding.rvProjects.layoutManager = LinearLayoutManager(requireContext())
        binding.rvProjects.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(ProjectsIntent.LoadProjects())
        }

        binding.tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                filterProjects(tab?.position ?: 0)
            }
            override fun onTabUnselected(tab: TabLayout.Tab?) {}
            override fun onTabReselected(tab: TabLayout.Tab?) {}
        })
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        binding.swipeRefresh.isRefreshing = state.isLoading
                        filterProjects(binding.tabLayout.selectedTabPosition)
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is ProjectsEffect.ShowError -> {
                                com.google.android.material.snackbar.Snackbar.make(binding.root, effect.message, com.google.android.material.snackbar.Snackbar.LENGTH_LONG).show()
                            }
                            is ProjectsEffect.ShowSuccess -> {
                                com.google.android.material.snackbar.Snackbar.make(binding.root, effect.message, com.google.android.material.snackbar.Snackbar.LENGTH_SHORT).show()
                            }
                        }
                    }
                }
            }
        }
    }

    private fun filterProjects(tabPosition: Int) {
        val allProjects = viewModel.state.value.projectsResponse?.data ?: emptyList()
        val filtered = if (tabPosition == 0) {
            allProjects.filter { it.status == "available" }
        } else {
            allProjects.filter { it.isMine }
        }
        adapter.submitList(filtered)
        
        val isEmpty = filtered.isEmpty() && !viewModel.state.value.isLoading
        binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
        binding.rvProjects.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
        
        binding.tvEmptyMessage.text = if (tabPosition == 0) {
            getString(com.nusatim.partner.features.projects.R.string.projects_empty_available)
        } else {
            getString(com.nusatim.partner.features.projects.R.string.projects_empty_mine)
        }
    }

    private fun showClaimConfirmation(project: com.nusatim.partner.core.model.dto.ProjectResponse) {
        MaterialAlertDialogBuilder(requireContext())
            .setTitle("Klaim Proyek")
            .setMessage("Apakah Anda yakin ingin mengklaim proyek '${project.name}'? Proyek ini mungkin sedang diperebutkan partner lain.")
            .setPositiveButton("Klaim") { _, _ ->
                viewModel.processIntent(ProjectsIntent.ClaimProject(project.id))
            }
            .setNegativeButton("Batal", null)
            .show()
    }
}
