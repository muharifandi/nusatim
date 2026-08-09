package com.nusatim.partner.features.projects.ui.detail

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.ProjectResponse
import com.nusatim.partner.features.projects.databinding.FragmentProjectDetailBinding
import com.nusatim.partner.features.projects.ui.board.ProjectsViewModel
import com.nusatim.partner.features.projects.ui.board.state.ProjectsEffect
import com.nusatim.partner.features.projects.ui.board.state.ProjectsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*

@AndroidEntryPoint
class ProjectDetailFragment : BaseFragment<FragmentProjectDetailBinding>() {

    private val viewModel: ProjectsViewModel by viewModels()
    private val projectId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.projects.R.string.project_detail_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        if (projectId != -1) {
            viewModel.processIntent(ProjectsIntent.LoadProjectDetail(projectId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        state.selectedProject?.let { renderProject(it) }
                    }
                }
                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is ProjectsEffect.ShowError -> {
                                com.google.android.material.snackbar.Snackbar.make(binding.root, effect.message, com.google.android.material.snackbar.Snackbar.LENGTH_LONG).show()
                            }
                            is ProjectsEffect.ShowSuccess -> {
                                com.google.android.material.snackbar.Snackbar.make(
                                    requireActivity().findViewById(android.R.id.content),
                                    effect.message,
                                    com.google.android.material.snackbar.Snackbar.LENGTH_SHORT
                                ).show()
                                findNavController().navigateUp()
                            }
                        }
                    }
                }
            }
        }
    }

    private fun renderProject(project: ProjectResponse) {
        binding.tvName.text = project.name
        binding.tvDescription.text = project.description
        binding.tvBudget.text = formatRupiah(project.budget)
        binding.tvCommission.text = formatRupiah(project.commissionValue)
        binding.tvLocation.text = project.location
        binding.tvDeadline.text = project.deadline
        
        binding.btnStatus.text = project.status.replaceFirstChar { it.uppercase() }
        
        // Action Button Logic
        when {
            project.status == "available" -> {
                binding.btnAction.text = getString(com.nusatim.partner.features.projects.R.string.project_btn_claim_long)
                binding.btnAction.visibility = android.view.View.VISIBLE
                binding.btnAction.setOnClickListener {
                    com.google.android.material.dialog.MaterialAlertDialogBuilder(requireContext())
                        .setTitle("Klaim Proyek")
                        .setMessage("Apakah Anda yakin ingin mengklaim proyek ini?")
                        .setPositiveButton("Ya, Klaim") { _, _ ->
                            viewModel.processIntent(ProjectsIntent.ClaimProject(project.id))
                        }
                        .setNegativeButton("Batal", null)
                        .show()
                }
            }
            project.isMine && project.status == "pending_approval" -> {
                binding.btnAction.text = getString(com.nusatim.partner.features.projects.R.string.projects_btn_cancel_claim)
                binding.btnAction.visibility = android.view.View.VISIBLE
                binding.btnAction.setOnClickListener {
                    viewModel.processIntent(ProjectsIntent.CancelClaimProject(project.id))
                }
            }
            project.isMine && (project.status == "assigned" || project.status == "in_progress") -> {
                binding.btnAction.text = getString(com.nusatim.partner.features.projects.R.string.project_btn_view_customer)
                binding.btnAction.visibility = android.view.View.VISIBLE
                binding.btnAction.setOnClickListener {
                    // Logic to find customer ID from project ID might be needed or deep link by project_id
                    findNavController().navigate("partner://customers/detail?project_id=${project.id}".toUri())
                }
            }
            else -> {
                binding.btnAction.visibility = android.view.View.GONE
            }
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
