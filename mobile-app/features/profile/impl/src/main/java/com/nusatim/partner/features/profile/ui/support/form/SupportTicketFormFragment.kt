package com.nusatim.partner.features.profile.ui.support.form

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.profile.databinding.FragmentSupportTicketFormBinding
import com.nusatim.partner.features.profile.ui.support.SupportTicketsViewModel
import com.nusatim.partner.features.profile.ui.support.state.SupportTicketsEffect
import com.nusatim.partner.features.profile.ui.support.state.SupportTicketsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class SupportTicketFormFragment : BaseFragment<FragmentSupportTicketFormBinding>() {

    private val viewModel: SupportTicketsViewModel by viewModels()

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.profile.R.string.support_form_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.btnSend.setOnClickListener {
            val subject = binding.etSubject.text.toString()
            val description = binding.etDescription.text.toString()
            if (subject.isNotEmpty() && description.isNotEmpty()) {
                viewModel.processIntent(SupportTicketsIntent.CreateTicket(subject, description))
            }
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        binding.btnSend.isEnabled = !state.isLoading
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is SupportTicketsEffect.SuccessCreate -> {
                                com.google.android.material.snackbar.Snackbar.make(
                                    requireActivity().findViewById(android.R.id.content),
                                    "Tiket berhasil dibuat",
                                    com.google.android.material.snackbar.Snackbar.LENGTH_SHORT
                                ).show()
                                findNavController().navigateUp()
                            }
                            is SupportTicketsEffect.ShowError -> {
                                com.google.android.material.snackbar.Snackbar.make(
                                    binding.root,
                                    effect.message,
                                    com.google.android.material.snackbar.Snackbar.LENGTH_LONG
                                ).show()
                            }
                        }
                    }
                }
            }
        }
    }
}
