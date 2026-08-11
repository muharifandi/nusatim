package com.nusatim.partner.features.auth.status.ui

import android.widget.Toast
import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.common.util.Constants
import com.nusatim.partner.core.ui.util.UiErrorHandler
import com.nusatim.partner.features.auth.status.databinding.FragmentApprovalStatusBinding
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ApprovalStatusFragment : BaseFragment<FragmentApprovalStatusBinding>() {

    private val viewModel: ApprovalStatusViewModel by viewModels()

    override fun onInitViews() {
        binding.btnRefresh.setOnClickListener {
            viewModel.refreshStatus()
        }
        binding.btnEditProfile.setOnClickListener {
            findNavController().navigate("partner://profile/edit".toUri())
        }
        binding.btnLogout.setOnClickListener {
            viewModel.logout()
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collectLatest { state ->
                        binding.btnRefresh.isEnabled = !state.isLoading
                        binding.btnRefresh.text = if (state.isLoading) {
                            getString(com.nusatim.partner.features.auth.status.R.string.status_btn_refreshing)
                        } else {
                            getString(com.nusatim.partner.features.auth.status.R.string.status_btn_refresh)
                        }

                        updateUi(state.status, state.rejectionReason)

                        if (state.isApproved) {
                            findNavController().navigate("partner://home".toUri())
                        }

                        if (state.isLoggedOut) {
                            findNavController().navigate("partner://login".toUri())
                        }

                        state.errorType?.let { type ->
                            val message = UiErrorHandler.getErrorMessage(requireContext(), type, state.error)
                            Toast.makeText(requireContext(), message, Toast.LENGTH_SHORT).show()
                            viewModel.dismissError()
                        }
                    }
                }
            }
        }
    }

    private fun updateUi(status: String?, rejectionReason: String?) {
        when (status) {
            Constants.Status.REJECTED -> {
                binding.ivStatusIcon.setImageResource(com.nusatim.partner.core.ui.R.drawable.ic_status_rejected)
                binding.tvStatusTitle.text = getString(com.nusatim.partner.features.auth.status.R.string.status_title_rejected)
                binding.tvStatusDesc.text = rejectionReason ?: getString(com.nusatim.partner.features.auth.status.R.string.status_desc_rejected)
            }
            Constants.Status.SUSPENDED -> {
                binding.ivStatusIcon.setImageResource(com.nusatim.partner.core.ui.R.drawable.ic_status_suspended)
                binding.tvStatusTitle.text = getString(com.nusatim.partner.features.auth.status.R.string.status_title_suspended)
                binding.tvStatusDesc.text = getString(com.nusatim.partner.features.auth.status.R.string.status_desc_suspended)
            }
            else -> {
                binding.ivStatusIcon.setImageResource(com.nusatim.partner.core.ui.R.drawable.ic_status_pending)
                binding.tvStatusTitle.text = getString(com.nusatim.partner.features.auth.status.R.string.status_title_pending)
                binding.tvStatusDesc.text = getString(com.nusatim.partner.features.auth.status.R.string.status_desc_pending)
            }
        }
    }
}
